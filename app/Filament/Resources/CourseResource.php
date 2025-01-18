<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $modelLabel ='Course';
    protected static ?string $navigationLabel = 'Courses';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Course Management';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required(),
                Forms\Components\Select::make('instructor_id')
                    ->relationship('instructor', 'name')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Select::make('discount_type')
                    ->options([
                        'percentage' => '%',
                        'amount' => 'amount'
                    ])
                    ->live(),
                Forms\Components\TextInput::make('discount_amount')
                    ->label('Discount Amount in EGP')
                    ->numeric()
                    ->disabled(fn (Forms\Get $get) => $get('discount_type') !== 'amount')
                    ->hidden(fn (Forms\Get $get) => $get('discount_type') !== 'amount')
                    ->required(fn (Forms\Get $get) => $get('discount_type') == 'amount')
                    ->rules('gte:0')
                    ->lte('price')
                    ->live(),

                Forms\Components\TextInput::make('discount_amount')
                    ->label('Discount Percentage 0 => 100 %')
                    ->numeric()
                    ->disabled(fn (Forms\Get $get) => $get('discount_type') == 'amount')
                    ->hidden(fn (Forms\Get $get) => $get('discount_type') == 'amount')
                    ->required(fn (Forms\Get $get) => $get('discount_type') !== 'amount')
                    ->rules('gte:0|lte:100')
                    ->live(),

                Forms\Components\Toggle::make('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('Lessons Count')
                    ->state(fn($record)=>$record->lessons->count())
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->formatStateUsing(fn (string $state): string => $state === 'amount' ? 'EGP' : '%')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_price')
                    ->state(fn ($record): string => $record->discount_type === 'amount'
                        ? $record->price - $record->discount_amount
                        : $record->price * (1 - $record->discount_amount/100))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}

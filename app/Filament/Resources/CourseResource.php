<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
                Forms\Components\Wizard::make()
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Wizard\Step::make('Basic Information')
                             ->schema(self::getBasicInformation())
                            ->icon('heroicon-o-information-circle'),
                        Forms\Components\Wizard\Step::make('Pricing Details')
                            ->schema(self::getPricingDetails())
                            ->icon('heroicon-o-currency-dollar'),
                        Forms\Components\Wizard\Step::make('Students and Start Time Details')
                            ->schema(self::getTimeAndStudentsDetails())
                            ->icon('heroicon-o-user'),
                        Forms\Components\Wizard\Step::make('Course Days Details')
                            ->schema(self::getCourseDaysDetails())
                            ->icon('heroicon-o-clock')
                            ->reactive()
                            ->visible(fn (Get $get) => $get('type') != 'recorded'),
                    ])->skippable(),
            ]);
    }
    public static function getTimeAndStudentsDetails():array
    {
        return [
            Forms\Components\DatePicker::make('started_at')
                ->label('Course Start Date')
                ->format('Y/m/d'),
            Forms\Components\TextInput::make('max_students')
                ->required()
                ->numeric()
                ->gt(0)
                ->prefixIcon('heroicon-o-user'),
        ];
    }
    public static function getCourseDaysDetails():array
    {
        return [
                Forms\Components\Repeater::make('days')
                    ->relationship('days')
                    ->schema([
                        Forms\Components\Select::make('day')
                            ->options([
                                'Saturday'=>'Saturday',
                                'Sunday'=>'Sunday',
                                'Monday'=>'Monday',
                                'Tuesday'=>'Tuesday',
                                'Wednesday'=>'Wednesday',
                                'Thursday'=>'Thursday',
                                'Friday'=>'Friday'
                            ])
                            ->required(),
                        Forms\Components\TimePicker::make('start_time')
                            ->hoursStep(1)
                            ->minutesStep(30)
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->hoursStep(1)
                            ->minutesStep(30)
                            ->after('start_time')
                            ->required(),
                    ])
        ];
    }

    public static function getPricingDetails():array
    {
        return [
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
        ];
    }
    public static function getBasicInformation():array
    {
        return [
            Forms\Components\FileUpload::make('thumbnail')
                ->label('Course Thumbnail')
                ->disk('s3')
                ->directory('courses')
                ->image()
                ->preserveFilenames()
                ->storeFiles(false)
                ->visibility('public')
                ->columnSpanFull(),
            Forms\Components\Select::make('type')
                ->options([
                    'online'=>'Online',
                    'recorded'=>'Recorded',
                    'offline'=>'Offline'
                ])
                ->reactive()
                ->required(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('branch_id')
                ->relationship('branch', 'name')
                ->required(),
            Forms\Components\Select::make('instructor_id')
                ->relationship('instructor', 'name')
                ->required(),
            Forms\Components\RichEditor::make('description')
                ->required()
                ->columnSpanFull(),
        ];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->alignCenter()
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->alignCenter()
                    ->searchable(),
                Tables\Columns\TextColumn::make('Lessons Count')
                    ->state(fn($record)=>$record->lessons->count())
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_students')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->alignCenter()
                    ->date(),
                Tables\Columns\TextColumn::make('days')
                    ->label('Schedule')
                    ->formatStateUsing(function ($record) {
                        return $record->days->map(function ($day) {
                            $start = Carbon::parse($day->start_time);
                            $end = Carbon::parse($day->end_time);

                            $hours = $start->diffInHours($end);
                            return "{$day->day}: {$hours} hours";
                        })->join(', ');
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->money('egp')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
//                Tables\Columns\TextColumn::make('discount_type')
//                    ->formatStateUsing(fn (string $state): string => $state === 'amount' ? 'EGP' : '%')
//                    ->alignCenter(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->state(fn ($record): string => $record->discount_type === 'amount'
                        ? '- '.$record->discount_amount
                        : '- '.$record->price * ($record->discount_amount/100))
                    ->badge()
                    ->color('danger')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_price')
                    ->money('egp')
                    ->state(fn ($record): string => $record->discount_type === 'amount'
                        ? $record->price - $record->discount_amount
                        : $record->price * (1 - $record->discount_amount/100))
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('active')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->alignCenter()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->alignCenter()
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

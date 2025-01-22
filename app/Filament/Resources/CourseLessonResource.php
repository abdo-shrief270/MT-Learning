<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseLessonResource\Pages;
use App\Filament\Resources\CourseLessonResource\RelationManagers;
use App\Models\CourseLesson;
use App\Models\Meeting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseLessonResource extends Resource
{
    protected static ?string $model = CourseLesson::class;
    protected static ?string $modelLabel ='Lesson';
    protected static ?string $navigationLabel = 'Course Lessons';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Course Management';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('thumbnail')
                    ->label('Lesson Thumbnail')
                    ->disk('s3')
                    ->directory('courseLessons')
                    ->image()
                    ->preserveFilenames()
                    ->storeFiles(false)
                    ->visibility('public')
                    ->columnSpanFull(),
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meeting_link')
                    ->state(fn($record) => Meeting::where('lesson_id',$record->id)->first()->url ?'link': 'Not Started Yet')
                    ->url(fn($record) => Meeting::where('lesson_id',$record->id)->first()->url ?? '')
                    ->icon('heroicon-o-link')
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->since()
                    ->sortable()
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
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseLessons::route('/'),
            'create' => Pages\CreateCourseLesson::route('/create'),
            'edit' => Pages\EditCourseLesson::route('/{record}/edit'),
        ];
    }
}

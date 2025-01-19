<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeetingResource\Pages;
use App\Filament\Resources\MeetingResource\RelationManagers;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Meeting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Lesson Meetings';
    protected static ?string $navigationGroup = 'Course Management';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->options(Course::pluck('title', 'id'))
                    ->reactive() // Make it reactive
                    ->required()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        $set('lesson_id', null);
                    }),
                Forms\Components\Select::make('lesson_id')
                    ->options(function (callable $get) {
                        $courseId = $get('course_id');
                        return $courseId ? CourseLesson::where('course_id', $courseId)->pluck('title', 'id') : [];
                    })
                    ->reactive() // Make it reactive
                    ->required()
                    ->label('Lesson')
                    ->disabled(fn ($get) => !$get('course_id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lesson.title')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('url')
                    ->label('Room URL')
                    ->url(fn ($record) => $record->url, true)
                    ->openUrlInNewTab(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Meeting')
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListMeetings::route('/'),
            'create' => Pages\CreateMeeting::route('/create'),
        ];
    }
}

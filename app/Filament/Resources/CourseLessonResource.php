<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseLessonResource\Pages;
use App\Filament\Resources\CourseLessonResource\RelationManagers;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\Meeting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

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
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('link')
                    ->visible(fn (Forms\Get $get) => Course::find($get('course_id'))?->type === 'recorded')
                    ->label('Lesson Video')
                    ->reactive()
                    ->disk('s3')
                    ->directory('courseLessons')
                    ->preserveFilenames()
                    ->storeFiles(false)
                    ->visibility('public')
                    ->required(fn (Forms\Get $get) => Course::find($get('course_id'))?->type === 'recorded')
                    ->maxSize(24576)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('link')
                    ->state(fn($record) => self::retriveLink($record))
                    ->url(fn($record) => self::retriveUrl($record),true)
                    ->icon('heroicon-o-link')
                    ->color(fn($record) => Meeting::where('lesson_id',$record->id)->first()?->url ?'success': ($record->link?'info':'primary'))
                    ->searchable(),
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

    protected static function retriveLink($record)
    {
        if(isset($record->link))
        {
            return 'Video Link';
        }
        return Meeting::where('lesson_id',$record->id)->first()?->url ?'Meeting Link':'Not Started Yet';
    }
    protected static function retriveUrl($record)
    {
        if(isset($record->link))
        {
            if(env('FILESYSTEM_DISK')=='s3'){
                return $record->link?Storage::disk(env('FILESYSTEM_DISK'))->url($record->link):null;

            }else{
                return $record->link?Storage::disk(env('FILESYSTEM_DISK'))->path($record->link):null;

            }
        }
        return Meeting::where('lesson_id',$record->id)->first()?->url;
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

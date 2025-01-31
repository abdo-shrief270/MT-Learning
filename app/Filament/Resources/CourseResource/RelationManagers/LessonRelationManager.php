<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use App\Services\S3UploadService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class LessonRelationManager extends RelationManager
{
    protected static string $relationship = 'lesson';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('thumbnail')
                    ->label('Lesson Thumbnail')
                    ->disk('s3')
                    ->directory('courseLessons')
                    ->preserveFilenames()
                    ->storeFiles(false)
                    ->visibility('public')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('link')
                    ->label('Lesson Video')
                    ->disk('s3')
                    ->directory('courseLessons')
                    ->preserveFilenames()
                    ->storeFiles(false)
                    ->visibility('public'),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->alignCenter()
                    ->toggleable()
                    ->circular(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('link')
                    ->toggleable()
                    ->label('Video Link')
                    ->state('Link')
                    ->icon('heroicon-o-link')
                    ->url(fn ($record) => Storage::disk(env('FILESYSTEM_DISK'))->url($record->link), true)
                    ->openUrlInNewTab(),
//                Tables\Columns\TextColumn::make('duration')
//                    ->toggleable(),
                Tables\Columns\ToggleColumn::make('active')
                    ->alignCenter()
                    ->toggleable(),
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
            ->reorderable('sort')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data = S3UploadService::upload($data, 'thumbnail', 'courseLessons');
                        $data['sort']=0; //Todo update this value
                        if (isset($data['link'])) {
                            //                            $getID3 = new \getID3;
//                            $file = $getID3->analyze(Storage::disk(env('FILESYSTEM_DISK'))->url($data['link']));
//                            dd($file);
//                            $data['duration'] = date('H:i:s.v', $file['playtime_seconds']);
                            return S3UploadService::upload($data, 'link', 'courseLessons');
                        }

                        return $data;
                    })])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Model $record, array $data): Model {
                        $data=S3UploadService::upload($data, 'thumbnail', 'courseLessons',$record,isset($record->thumbnail));
                        if(isset($data['link']))
                        {
                            $data= S3UploadService::upload($data, 'link', 'courseLessons',$record,isset($record->link));
                        }
                        $record->update($data);
                        return $record;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

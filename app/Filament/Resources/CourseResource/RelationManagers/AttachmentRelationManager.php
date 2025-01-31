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

class AttachmentRelationManager extends RelationManager
{
    protected static string $relationship = 'attachment';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('link')
                    ->label('Lesson Attachment')
                    ->disk('s3')
                    ->directory('courseAttachments')
                    ->preserveFilenames()
                    ->storeFiles(false)
                    ->visibility('public'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('adder.name')
                    ->label('Added By'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('link')
                    ->label('Download Link')
                    ->state('Link')
                    ->icon('heroicon-o-link')
                    ->url(fn ($record) => Storage::disk(env('FILESYSTEM_DISK'))->url($record->link), true)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['added_by'] = auth()->id();
                        return S3UploadService::upload($data, 'link', 'courseAttachments');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->using(function (Model $record, array $data): Model {
                    $data=S3UploadService::upload($data, 'link', 'courseAttachments',$record,isset($record->link));
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

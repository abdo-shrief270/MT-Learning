<?php

namespace App\Filament\Resources\CourseLessonResource\Pages;

use App\Filament\Resources\CourseLessonResource;
use App\Services\S3UploadService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseLesson extends EditRecord
{
    protected static string $resource = CourseLessonResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        $data=S3UploadService::upload($data, 'thumbnail', 'courseLessons',$record,true);
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

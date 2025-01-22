<?php

namespace App\Filament\Resources\CourseLessonResource\Pages;

use App\Filament\Resources\CourseLessonResource;
use App\Services\S3UploadService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseLesson extends CreateRecord
{
    protected static string $resource = CourseLessonResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data=S3UploadService::upload($data, 'thumbnail', 'courseLessons');
        if(isset($data['link']))
        {
            return S3UploadService::upload($data, 'link', 'courseLessons');
        }
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

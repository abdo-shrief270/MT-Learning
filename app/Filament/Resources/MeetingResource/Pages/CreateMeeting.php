<?php

namespace App\Filament\Resources\MeetingResource\Pages;

use App\Filament\Resources\MeetingResource;
use App\Models\CourseLesson;
use App\Models\Meeting;
use App\Services\DailyService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateMeeting extends CreateRecord
{
    protected static string $resource = MeetingResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $lesson=CourseLesson::find($data['lesson_id']);
        $data['name']=Str::slug($lesson->title);
        $dailyService=new DailyService;
        $response = $dailyService->createMeeting([
            'name' => $data['name'],
        ]);
        $data['url'] =$response['url'];
        return $data;
    }
    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }
        $record->save();
        Notification::make()
            ->title($record->name.' Meeting')
            ->body('Meeting Created successfully by : '.auth()->user()->name)
            ->success()
            ->icon('heroicon-o-video-camera')
            ->send()
            ->sendToDatabase(auth()->user());
        return $record;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

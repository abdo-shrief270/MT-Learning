<?php

namespace App\Filament\Resources\MeetingResource\Pages;

use App\Filament\Resources\MeetingResource;
use App\Models\Meeting;
use Filament\Resources\Pages\Page;

class ViewMeeting extends Page
{
    protected static string $resource = MeetingResource::class;

    protected static string $view = 'filament.resources.meeting-resource.pages.view-meeting';

    public Meeting $meeting;

    public function mount(Meeting $record)
    {
        $this->meeting = $record;
    }

    public function getTitle(): string
    {
        return 'Viewing: ' . $this->meeting->name;
    }
}

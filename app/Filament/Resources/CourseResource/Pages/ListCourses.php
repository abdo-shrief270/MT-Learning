<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'online' => Tab::make('Online')->query(fn ($query) => $query->where('type','online')),
            'offline' => Tab::make('Offline')->query(fn ($query) => $query->where('type','offline')),
            'recorded' => Tab::make('Recorded')->query(fn ($query) => $query->where('type','recorded')),
        ];
    }
}

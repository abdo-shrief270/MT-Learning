<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        $data= [
            null => Tab::make('All')->query(fn ($query) => $query)
        ];
        foreach (\Spatie\Permission\Models\Role::orderBy('id', 'ASC')->select('name')->pluck('name')->all() as $role)
        {
            $data[$role] = Tab::make()->query(fn ($query) => $query->role($role));
        }
        return $data;
    }
}

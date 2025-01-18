<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['password'] = null;
        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if($data['password']==null){
            unset($data['password']);
        }
        if(isset($data['password']))
        {
            $data['password'] = Hash::make($data['password']);
        }
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

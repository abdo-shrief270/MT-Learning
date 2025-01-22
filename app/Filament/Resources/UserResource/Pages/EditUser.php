<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Services\S3UploadService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['roles']=User::find($data['id'])->roles?->pluck('name');
        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record=$this->getRecord();
        unset($data['password']);
        return S3UploadService::upload($data, 'avatar_url', 'avatars',$record,isset($record->avatar_url));
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        foreach ($record->roles->pluck('name') as $role)
        {
            $record->removeRole($role);
        }
        $record->assignRole($data['roles']);
        return $record;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

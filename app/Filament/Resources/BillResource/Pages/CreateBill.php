<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Services\S3UploadService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
       $data['added_by']=auth()->user()->id;
        return S3UploadService::upload($data, 'image', 'bills');
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

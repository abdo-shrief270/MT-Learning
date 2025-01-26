<?php

namespace App\Filament\Resources\BillTypeResource\Pages;

use App\Filament\Resources\BillTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBillType extends CreateRecord
{
    protected static string $resource = BillTypeResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

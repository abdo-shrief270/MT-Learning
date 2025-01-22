<?php

namespace App\Filament\Pages;

use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class AccountNotActivated extends SimplePage
{
//    protected static ?string $navigationIcon = 'heroicon-o-ban';

    protected static string $view = 'filament.resources.auth-resource.pages.account-not-activated';
    public function getTitle(): string | Htmlable
    {
        return 'Account Not Activated';
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString("<h1 class='text-2xl font-bold text-danger-600'>Account Not Activated</h1>");
    }
}

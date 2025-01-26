<?php
namespace App\Filament\Resources;

use Vormkracht10\FilamentMails\Resources\MailResource as BaseMailResource;

class CustomMailResource extends BaseMailResource
{
    public static function getNavigationGroup(): ?string
    {
        return __('Application Logs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Mails');
    }

    public static function getLabel(): ?string
    {
        return __('Mail');
    }

    public function getTitle(): string
    {
        return __('Mails');
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

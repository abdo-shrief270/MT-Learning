<?php
namespace App\Filament\Resources;
use App\Models\Event;
use Vormkracht10\FilamentMails\Resources\EventResource as BaseEventResource;

class CustomEventResource extends BaseEventResource
{
    protected static ?string $model = Event::class;
    public function getTitle(): string
    {
        return __('Events');
    }

    public static function getNavigationParentItem(): ?string
    {
        return null;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Application Logs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Events');
    }

    public static function getLabel(): ?string
    {
        return __('Event');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Events');
    }
}

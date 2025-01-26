<?php

use App\Filament\Resources\CustomMailResource;
use App\Filament\Resources\CustomEventResource;

return [
    'resources' => [
        'mail' => CustomMailResource::class,
        'event' => CustomEventResource::class,
    ],
];

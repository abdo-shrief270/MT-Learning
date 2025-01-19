<?php

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'filament'])
    ->get('/meetings/{meeting}/view', \App\Filament\Resources\MeetingResource\Pages\ViewMeeting::class)
    ->name('filament.resources.meeting-resource.view');

<?php

use Illuminate\Support\Facades\Route;
Route::get('/not-activated', \App\Filament\Pages\AccountNotActivated::class)->name('account-not-activated')->middleware(\App\Http\Middleware\VerifyActivationUser::class);
\Vormkracht10\FilamentMails\FilamentMails::routes();

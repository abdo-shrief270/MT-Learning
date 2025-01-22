<?php

use Vormkracht10\FilamentMails\Controllers\MailDownloadController;
use Vormkracht10\FilamentMails\Controllers\MailPreviewController;
use Illuminate\Support\Facades\Route;
Route::get('/not-activated', \App\Filament\Pages\AccountNotActivated::class)->name('account-not-activated')->middleware(\App\Http\Middleware\VerifyActivationUser::class);
Route::get('mails/{mail}/preview', MailPreviewController::class)->name('mails.preview');
Route::get('mails/{mail}/attachment/{attachment}/{filename}', MailDownloadController::class)->name('mails.attachment.download');

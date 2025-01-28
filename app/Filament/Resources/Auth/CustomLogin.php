<?php

namespace App\Filament\Resources\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login;
use Illuminate\Validation\ValidationException;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class CustomLogin extends Login
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getLoginTypeFormComponent(),
                        $this->getLoginEmailFormComponent(),
                        $this->getLoginPhoneFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getLoginTypeFormComponent(): Component
    {
        return Checkbox::make('type')
            ->label(__('login with phone ?'))
            ->live();
    }
    protected function getLoginEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('Email Address'))
            ->reactive()
            ->visible(fn (Get $get) => (!$get('type')))
            ->required(fn (Get $get) => (!$get('type')))
            ->autocomplete()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getLoginPhoneFormComponent(): Component
    {
        return PhoneInput::make('login')
            ->label(__('Phone Number'))
            ->reactive()
            ->visible(fn (Get $get) => ($get('type')))
            ->required(fn (Get $get) => ($get('type')))
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'],FILTER_VALIDATE_EMAIL)?'email':'phone';
        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}

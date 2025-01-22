<?php

namespace App\Filament\Resources\Auth;

use App\Services\S3UploadService;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Register extends \Filament\Pages\Auth\Register
{
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        return S3UploadService::upload($data, 'avatar_url', 'avatars');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getAvatarFormComponent(),
                        $this->getNameFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getRoleFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getPhoneFormComponent(): Component
    {
        return PhoneInput::make('phone')
            ->label('Phone Number')
            ->required()
            ->unique(ignoreRecord: true);
    }

    protected function getRoleFormComponent(): Component
    {
        return Select::make('Role')
            ->label('Your Role?')
            ->options(fn () => \Spatie\Permission\Models\Role::query()->whereNotIn('name',['super_admin','admin'])->orderBy('id', 'ASC')->pluck('name', 'name'))
            ->required();
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar_url')
            ->label('Avatar')
            ->disk('s3')
            ->directory('avatars')
            ->image()
            ->preserveFilenames()
            ->storeFiles(false)
            ->visibility('public');
    }
}

<?php

namespace App\Filament\Resources\Auth;

use App\Services\S3UploadService;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Register extends \Filament\Pages\Auth\Register
{
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        return S3UploadService::upload($data, 'avatar_url', 'avatars');
    }
    protected function handleRegistration(array $data): Model
    {
        $user= $this->getUserModel()::create($data);
        $user->assignRole($data['role']);
        return $user;
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
        return Select::make('role')
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

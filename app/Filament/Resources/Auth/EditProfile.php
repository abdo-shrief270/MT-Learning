<?php

namespace App\Filament\Resources\Auth;

use App\Services\S3UploadService;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EditProfile extends \Filament\Pages\Auth\EditProfile
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['password']=null;
        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = auth()->user();
        return S3UploadService::upload($data, 'avatar_url', 'avatars',$record,isset($record->avatar_url));
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
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
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

<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Services\S3UploadService;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Resources\Concerns\HasTabs;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Laravel\Pail\ValueObjects\Origin\Console;

class CreateCourse extends CreateRecord
{
    use HasWizard;
    protected static string $resource = CourseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSteps():array
    {
        return [
            Step::make('Basic Information')
                ->schema([Section::make()->schema(CourseResource::getBasicInformation())->columns()])
                ->icon('heroicon-o-information-circle'),
            Step::make('Pricing Details')
                ->schema([Section::make()->schema(CourseResource::getPricingDetails())->columns()])
                ->icon('heroicon-o-currency-dollar'),
            Step::make('Students and Start Time Details')
                ->schema([Section::make()->schema(CourseResource::getTimeAndStudentsDetails())->columns()])
                ->icon('heroicon-o-user'),
            Step::make('Course Days')
                ->schema([Section::make()->schema(CourseResource::getCourseDaysDetails())->columns()])
                ->icon('heroicon-o-clock')
                ->reactive()
                ->visible(fn (Get $get) => $get('type') != 'recorded'),
            Step::make('Course Lessons')
                ->schema([Section::make()->schema(CourseResource::getCourseLessonsDetails())->columns()])
                ->icon('heroicon-o-video-camera'),
        ];
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return S3UploadService::upload($data, 'thumbnail', 'courses');
    }
}

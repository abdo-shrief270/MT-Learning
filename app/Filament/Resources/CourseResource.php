<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers\AttachmentRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\LessonRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\MeetingRelationManager;
use App\Models\Course;
use App\Services\S3UploadService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $modelLabel ='Course';
    protected static ?string $navigationLabel = 'Courses';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Course Management';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make()
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Wizard\Step::make('Basic Information')
                            ->schema(self::getBasicInformation())
                            ->icon('heroicon-o-information-circle'),
                        Forms\Components\Wizard\Step::make('Pricing Details')
                            ->schema(self::getPricingDetails())
                            ->icon('heroicon-o-currency-dollar'),
                        Forms\Components\Wizard\Step::make('Students and Start Time Details')
                            ->visible(fn (Get $get) => $get('type') != 'recorded')
                            ->schema(self::getTimeAndStudentsDetails())
                            ->icon('heroicon-o-user'),
//                        Forms\Components\Wizard\Step::make('Course Days')
//                            ->schema(self::getCourseDaysDetails())
//                            ->icon('heroicon-o-clock')
//                            ->reactive()
//                            ->visible(fn (Get $get) => $get('type') != 'recorded'),
//                        Forms\Components\Wizard\Step::make('Course Lessons')
//                            ->schema(self::getCourseLessonsDetails())
//                            ->icon('heroicon-o-video-camera')
//                            ->reactive()
//                            ->visible(fn (Get $get) => $get('type') == 'online'),
                    ])->skippable(),
            ]);
    }
    public static function getTimeAndStudentsDetails():array
    {
        return [
            Forms\Components\DatePicker::make('started_at')
                ->visible(fn (Get $get) => $get('type') != 'recorded')
                ->required(fn (Get $get) => $get('type') != 'recorded')
                ->label('Course Start Date')
                ->format('Y/m/d'),
            Forms\Components\TextInput::make('max_students')
                ->required(fn (Get $get) => $get('type') != 'recorded')
                ->default(0)
                ->visible(fn (Get $get) => $get('type') != 'recorded')
                ->reactive()
                ->numeric()
                ->gt(0)
                ->prefixIcon('heroicon-o-user'),
        ];
    }
    public static function getCourseDaysDetails():array
    {
        return [
                Forms\Components\Repeater::make('days')
                    ->relationship('days')
                    ->schema([
                        Forms\Components\Select::make('day')
                            ->options([
                                'Saturday'=>'Saturday',
                                'Sunday'=>'Sunday',
                                'Monday'=>'Monday',
                                'Tuesday'=>'Tuesday',
                                'Wednesday'=>'Wednesday',
                                'Thursday'=>'Thursday',
                                'Friday'=>'Friday'
                            ])
                            ->required(),
                        Forms\Components\TimePicker::make('start_time')
                            ->hoursStep(1)
                            ->minutesStep(30)
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->hoursStep(1)
                            ->minutesStep(30)
                            ->after('start_time')
                            ->required(),
                    ])->orderColumn('id')
        ];
    }

    public static function getCourseLessonsDetails():array
    {
        return [
            Forms\Components\Repeater::make('lessons')
                ->relationship('lessons')
                ->schema([
                    Forms\Components\Wizard::make()
                    ->schema(
                        [
                            Forms\Components\Wizard\Step::make('Lesson Image')
                            ->schema([
                                Forms\Components\FileUpload::make('thumbnail')
                                    ->label('Lesson Thumbnail')
                                    ->disk('s3')
                                    ->directory('courseLessons')
                                    ->preserveFilenames()
                                    ->storeFiles(false)
                                    ->visibility('public')
                                    ->required(),
                            ])
                            ->columnSpanFull(),

                            Forms\Components\Wizard\Step::make('Lesson Details')
                            ->schema([
                                Forms\Components\FileUpload::make('link')
                                    ->visible(fn (Forms\Get $get) => $get('../../type') === 'recorded')
                                    ->label('Lesson Video')
                                    ->reactive()
                                    ->disk('s3')
                                    ->directory('courseLessons')
                                    ->preserveFilenames()
                                    ->storeFiles(false)
                                    ->visibility('public')
                                    ->required(fn (Forms\Get $get) => $get('../../type') === 'recorded')
                                    ->maxSize(24576),
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->required()
                                    ->columns(1),
                            ])
                            ->columnSpanFull(),
                        ]
                    )
                    ->skippable()
                    ->columnSpanFull(),
                ])->orderColumn('id')
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                    $data=S3UploadService::upload($data, 'thumbnail', 'courseLessons');
                    if(isset($data['link']))
                    {
                        return S3UploadService::upload($data, 'link', 'courseLessons');
                    }
                    return $data;
                })
                ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                    $data=S3UploadService::upload($data, 'thumbnail', 'courseLessons');
                    if(isset($data['link']))
                    {
                        return S3UploadService::upload($data, 'link', 'courseLessons');
                    }
                    return $data;
                }),
        ];
    }


    public static function getPricingDetails():array
    {
        return [
            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('$'),
            Forms\Components\Select::make('discount_type')
                ->options([
                    'percentage' => '%',
                    'amount' => 'amount'
                ])
                ->live(),
            Forms\Components\TextInput::make('discount_amount')
                ->label('Discount Amount in EGP')
                ->numeric()
                ->disabled(fn (Forms\Get $get) => $get('discount_type') !== 'amount')
                ->hidden(fn (Forms\Get $get) => $get('discount_type') !== 'amount')
                ->required(fn (Forms\Get $get) => $get('discount_type') == 'amount')
                ->rules('gte:0')
                ->lte('price')
                ->live(),

            Forms\Components\TextInput::make('discount_amount')
                ->label('Discount Percentage 0 => 100 %')
                ->numeric()
                ->disabled(fn (Forms\Get $get) => $get('discount_type') == 'amount')
                ->hidden(fn (Forms\Get $get) => $get('discount_type') == 'amount')
                ->required(fn (Forms\Get $get) => $get('discount_type') !== 'amount')
                ->rules('gte:0|lte:100')
                ->live(),
        ];
    }
    public static function getBasicInformation():array
    {
        return [
            Forms\Components\FileUpload::make('thumbnail')
                ->required()
                ->label('Course Thumbnail')
                ->disk('s3')
                ->directory('courses')
                ->image()
                ->preserveFilenames()
                ->storeFiles(false)
                ->visibility('public')
                ->columnSpanFull(),
            Forms\Components\Select::make('type')
                ->options([
                    'online'=>'Online',
                    'recorded'=>'Recorded',
                    'offline'=>'Offline'
                ])
                ->reactive()
                ->required(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('instructor_id')
                ->relationship('instructor', 'name')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('branch_id')
                ->relationship('branch', 'name')
                ->required(fn (Get $get) => $get('type') != 'recorded')
                ->reactive()
                ->visible(fn (Get $get) => $get('type') != 'recorded'),
            Forms\Components\RichEditor::make('description')
                ->required()
                ->columnSpanFull(),
        ];
    }
    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->alignCenter()
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->alignCenter()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->state(fn($record)=>$record->branch->name)
                    ->alignCenter()
                    ->sortable()
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'offline')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('instructor.name')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('Lessons Count')
                    ->state(fn($record) => $record->lesson->count())
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'recorded')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('Meetings Count')
                    ->state(fn($record) => $record->meeting->count())
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'online')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('Attachments Count')
                    ->state(fn($record) => $record->attachment->count())
                    ->hidden(fn ($livewire) => $livewire->activeTab !== 'offline')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('max_students')
                    ->hidden(fn ($livewire) => $livewire->activeTab === 'recorded')
                    ->numeric()
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('started_at')
                    ->hidden(fn ($livewire) => $livewire->activeTab === 'recorded')
                    ->alignCenter()
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('days')
                    ->label('Schedule')
                    ->formatStateUsing(function ($record) {
                        return $record->days->map(function ($day) {
                            $start = Carbon::parse($day->start_time);
                            $end = Carbon::parse($day->end_time);

                            $hours = $start->diffInHours($end);
                            return "{$day->day}: {$hours} hours";
                        })->join(', ');
                    })
                    ->hidden(fn ($livewire) => $livewire->activeTab === 'recorded')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('egp')
                    ->badge()
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->state(fn ($record): string => $record->discount_type === 'amount'
                        ? '- '.$record->discount_amount
                        : '- '.$record->price * ($record->discount_amount/100))
                    ->badge()
                    ->color('danger')
                    ->alignCenter()
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('final_price')
                    ->money('egp')
                    ->state(fn ($record): string => $record->discount_type === 'amount'
                        ? $record->price - $record->discount_amount
                        : $record->price * (1 - $record->discount_amount/100))
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\ToggleColumn::make('active')
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->alignCenter()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->alignCenter()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])->recordAction(null)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function getRelations(): array
    {
        $record = request()->route('record');

        if (!$record) {
            return [];
        }
        $course = static::getModel()::find($record);
        return match ($course->type) {
            'online' => [MeetingRelationManager::class],
            'offline' => [AttachmentRelationManager::class],
            'recorded' => [LessonRelationManager::class],
            default => [],
        };
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
            'view' => Pages\ViewCourse::route('/{record}'),
        ];
    }
}

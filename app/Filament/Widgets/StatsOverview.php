<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Branchs', Branch::count())
                ->icon('heroicon-o-tag'),
            Stat::make('All Users', User::count())
                ->icon('heroicon-o-user-group'),
            Stat::make('Roles', Role::count())
                ->icon('heroicon-o-shield-check')
                ->description('All Filament Shield Roles')
                ->descriptionColor('primary'),
            Stat::make('Super Admins', User::role('super_admin')->count())
                ->icon('heroicon-o-users'),
            Stat::make('admins', User::role('admin')->count())
                ->icon('heroicon-o-users'),
            Stat::make('Instructors', User::role('instructor')->count())
                ->icon('heroicon-o-users'),
            Stat::make('Courses', Course::count())
                ->icon('heroicon-o-squares-2x2'),
            Stat::make('Course Lessons', CourseLesson::count())
                ->icon('heroicon-o-video-camera'),
            Stat::make('Students', User::role('student')->count())
                ->icon('heroicon-o-users'),
        ];
    }
}

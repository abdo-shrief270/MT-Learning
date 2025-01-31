<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('shield:install admin');
        Artisan::call('shield:generate --all --panel=admin');

        User::create([
            'name' => 'Abdelrahman Shrief Ali',
            'phone'=>'+201270989676',
            'email' => 'abdo.shrief270@gmail.com',
            'password' => bcrypt('12345678'),
            'email_verified_at'=>'2025-01-22 04:36:58',
            'active' => true
        ]);

        User::create([
            'name' => 'Ali Osama',
            'phone'=>'+201111492219',
            'email' => 'dev.ali@mt-school.online',
            'password' => bcrypt('12345678'),
            'email_verified_at'=>'2025-01-22 04:36:58',
            'active' => true
        ]);

        User::create([
            'name' => 'Mahmoud El-toukhy',
            'phone'=>'+201113261067',
            'email' => 'ceo.mahmoud@mt-school.online',
            'password' => bcrypt('12345678'),
            'email_verified_at'=>'2025-01-22 04:36:58',
            'active' => true
        ]);

        \Spatie\Permission\Models\Role::create([
            'name'=>'admin',
            'guard_name'=>'web'
        ]);
        \Spatie\Permission\Models\Role::create([
            'name'=>'instructor',
            'guard_name'=>'web'
        ]);
        \Spatie\Permission\Models\Role::create([
            'name'=>'student',
            'guard_name'=>'web'
        ]);
        \Spatie\Permission\Models\Role::create([
            'name'=>'sales',
            'guard_name'=>'web'
        ]);

        Artisan::call('shield:super-admin --user=1');
    }
}

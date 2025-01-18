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

        User::factory()->create([
            'name' => 'Abdelrahman Shrief Ali',
            'email' => 'dev.abdo.shrief@mt-school.online',
            'password' => bcrypt('12345678'),
            'active' => true
        ]);

        User::factory()->create([
            'name' => 'Ali Osama',
            'email' => 'dev.ali@mt-school.online',
            'password' => bcrypt('12345678'),
            'active' => true
        ]);

        User::factory()->create([
            'name' => 'Mahmoud El-toukhy',
            'email' => 'ceo.mahmoud@mt-school.online',
            'password' => bcrypt('12345678'),
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

        Artisan::call('shield:super-admin --user=1');
    }
}

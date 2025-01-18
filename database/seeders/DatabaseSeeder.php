<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

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
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        $admin = [
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 1,
        ];

        User::create($admin);

        /*$this->call([
            BarangaySeeder::class,
            ProvinceSeeder::class, // Example seeder for provinces
            CitySeeder::class,     // Example seeder for cities
            RegionSeeder::class,  
        ]);*/

    }
}

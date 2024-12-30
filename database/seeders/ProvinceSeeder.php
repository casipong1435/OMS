<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(public_path('Data/json/refprovince.json'));
        $data = json_decode($json, true);

        // Insert each barangay record into the database
        foreach ($data['RECORDS'] as $record) {
            DB::table('provinces')->insert([
                'psgcCode' => $record['psgcCode'],
                'provDesc' => $record['provDesc'],
                'regCode' => $record['regCode'],
                'provCode' => $record['provCode'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        //php artisan migrate --path=/database/migrations/2024_10_31_124139_create_cities_table.php
        //php artisan migrate --path=/database/migrations/2024_10_31_124123_create_provinces_table.php
        //php artisan migrate --path=/database/migrations/2024_10_31_124107_create_regions_table.php
        //php artisan migrate --path=/database/migrations/2024_10_26_144254_create_barangays_table.php

    }
}

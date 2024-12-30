<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(public_path('Data/json/refregion.json'));
        $data = json_decode($json, true);

        // Insert each barangay record into the database
        foreach ($data['RECORDS'] as $record) {
            DB::table('regions')->insert([
                'regCode' => $record['regCode'],
                'regDesc' => $record['regDesc'],
                'psgcCode' => $record['psgcCode'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

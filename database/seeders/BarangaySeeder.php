<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $json = file_get_contents(public_path('Data/json/refbrgy.json'));
        $data = json_decode($json, true);

        // Insert each barangay record into the database
        foreach ($data['RECORDS'] as $record) {
            DB::table('barangays')->insert([
                'brgyCode' => $record['brgyCode'],
                'brgyDesc' => $record['brgyDesc'],
                'regCode' => $record['regCode'],
                'provCode' => $record['provCode'],
                'citymunCode' => $record['citymunCode'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

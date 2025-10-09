<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $districts = [
            // Western Province
            ['name' => 'Colombo'], //1
            ['name' => 'Gampaha'], //2
            ['name' => 'Kalutara'], //3

            // Central Province
            ['name' => 'Kandy'],
            ['name' => 'Matale'],
            ['name' => 'Nuwara Eliya'],

            // Southern Province
            ['name' => 'Galle'],
            ['name' => 'Matara'],
            ['name' => 'Hambantota'],

            // Northern Province
            ['name' => 'Jaffna'],
            ['name' => 'Kilinochchi'],
            ['name' => 'Mannar'],
            ['name' => 'Mullaitivu'],
            ['name' => 'Vavuniya'],

            // Eastern Province
            ['name' => 'Trincomalee'],
            ['name' => 'Batticaloa'],
            ['name' => 'Ampara'],

            // North Western Province
            ['name' => 'Kurunegala'],
            ['name' => 'Puttalam'],

            // North Central Province
            ['name' => 'Anuradhapura'],
            ['name' => 'Polonnaruwa'],

            // Uva Province
            ['name' => 'Badulla'],
            ['name' => 'Monaragala'],

            // Sabaragamuwa Province
            ['name' => 'Ratnapura'],
            ['name' => 'Kegalle'],
        ];

        DB::table('districts')->insertOrIgnore($districts);
    }
}

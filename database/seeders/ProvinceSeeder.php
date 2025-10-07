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
         $provinces = [
            ['name' => 'Western Province'],
            ['name' => 'Central Province'],
            ['name' => 'Southern Province'],
            ['name' => 'Northern Province'],
            ['name' => 'Eastern Province'],
            ['name' => 'North Western Province'],
            ['name' => 'North Central Province'],
            ['name' => 'Uva Province'],
            ['name' => 'Sabaragamuwa Province'],
        ];

        DB::table('provinces')->insertOrIgnore($provinces);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LivingInBusNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $living_in_bus_names = [
            ['name' => 'Kinnadeniya 1'],
            ['name' => 'Kinnadeniya 2'],
            ['name' => 'Kinnadeniya 3'],
            ['name' => 'Panagoda - Officers'],
            ['name' => 'Panagoda - Other Ranks'],
            ['name' => 'Kandalanda'],
            ['name' => 'Pamankada'],
            ['name' => 'Maharagama'],
            ['name' => 'Mattegoda'],
            ['name' => 'SLEME - Kompanyaveediya'],
            ['name' => 'Rathmalana'],
            ['name' => 'Other'],
        ];

        DB::table('living_in_buses')->insertOrIgnore($living_in_bus_names);
    }
}

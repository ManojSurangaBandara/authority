<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DestinationLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destination_locations = [
            ['destination_location' => 'Panagoda'],
            ['destination_location' => 'Kandalanda'],
            ['destination_location' => 'Maharagama'],
            ['destination_location' => 'Kinnadeniya'],
            ['destination_location' => 'Pamankada'],
            ['destination_location' => 'Mattegoda'],
            ['destination_location' => 'SLEME Workshop'],
            ['destination_location' => 'Rathmalana'],
            ['destination_location' => 'Other'],
        ];

        DB::table('destination_locations')->insertOrIgnore($destination_locations);
    }
}

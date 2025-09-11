<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusType;
use App\Models\Bus;
use App\Models\BusRoute;

class BusDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Bus Types
        $busTypes = [
            ['name' => 'Express'],
            ['name' => 'Normal'],
            ['name' => 'Semi-Luxury'],
            ['name' => 'Luxury'],
        ];

        foreach ($busTypes as $type) {
            BusType::firstOrCreate(['name' => $type['name']], $type);
        }

        // Create Buses
        $expressType = BusType::where('name', 'Express')->first();
        $normalType = BusType::where('name', 'Normal')->first();

        $buses = [
            [
                'no' => 'BUS-001',
                'name' => 'Colombo Express',
                'type_id' => $expressType->id,
                'no_of_seats' => 45,
            ],
            [
                'no' => 'BUS-002',
                'name' => 'Kandy Normal',
                'type_id' => $normalType->id,
                'no_of_seats' => 50,
            ],
            [
                'no' => 'BUS-003',
                'name' => 'Galle Express',
                'type_id' => $expressType->id,
                'no_of_seats' => 40,
            ],
        ];

        foreach ($buses as $bus) {
            Bus::firstOrCreate(['no' => $bus['no']], $bus);
        }

        // Create Bus Routes
        $bus1 = Bus::where('no', 'BUS-001')->first();
        $bus2 = Bus::where('no', 'BUS-002')->first();
        $bus3 = Bus::where('no', 'BUS-003')->first();

        $routes = [
            [
                'name' => 'Colombo - Kandy',
                'bus_id' => $bus1->id,
            ],
            [
                'name' => 'Kandy - Anuradhapura',
                'bus_id' => $bus2->id,
            ],
            [
                'name' => 'Colombo - Galle',
                'bus_id' => $bus3->id,
            ],
        ];

        foreach ($routes as $route) {
            BusRoute::firstOrCreate(['name' => $route['name']], $route);
        }
    }
}

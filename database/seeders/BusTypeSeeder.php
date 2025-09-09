<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Bus;
use App\Models\BusType;

class BusTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BusType::create(['name' => 'Army']);
        BusType::create(['name' => 'Civil']);
    }
}

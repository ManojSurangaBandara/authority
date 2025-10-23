<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rank;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranks = [
            ['abb_name' => 'Field Marshal', 'full_name' => 'Field Marshal'],
            ['abb_name' => 'Gen', 'full_name' => 'General'],
            ['abb_name' => 'Lt Gen', 'full_name' => 'Lieutenant General'],
            ['abb_name' => 'Maj Gen', 'full_name' => 'Major General'],
            ['abb_name' => 'Brig', 'full_name' => 'Brigadier'],
            ['abb_name' => 'Col', 'full_name' => 'Colonel'],
            ['abb_name' => 'Lt Col', 'full_name' => 'Lieutenant Colonel'],
            ['abb_name' => 'Lt Col (QM)', 'full_name' => 'Lieutenant Colonel (Quarter Master)'],
            ['abb_name' => 'Maj', 'full_name' => 'Major'],
            ['abb_name' => 'Maj (QM)', 'full_name' => 'Major (Quarter Master)'],
            ['abb_name' => 'Capt', 'full_name' => 'Captain'],
            ['abb_name' => 'Capt (QM)', 'full_name' => 'Captain (Quarter Master)'],
            ['abb_name' => 'Lt', 'full_name' => 'Lieutenant'],
            ['abb_name' => 'Lt (QM)', 'full_name' => 'Lieutenant (Quarter Master)'],
            ['abb_name' => '2/Lt', 'full_name' => 'Second Lieutenant'],
            ['abb_name' => 'PO', 'full_name' => 'Probation Officer'],
            ['abb_name' => 'O/Cdt', 'full_name' => 'Officer Cadet'],
            ['abb_name' => 'WO I', 'full_name' => 'Warrant Officer Class I'],
            ['abb_name' => 'WO II', 'full_name' => 'Warrant Officer Class II'],
            ['abb_name' => 'S/Sgt', 'full_name' => 'Staff Sergeant'],
            ['abb_name' => 'Sgt', 'full_name' => 'Sergeant'],
            ['abb_name' => 'Cpl', 'full_name' => 'Corporal'],
            ['abb_name' => 'L/Cpl', 'full_name' => 'Lance Corporal'],
            ['abb_name' => 'Pte', 'full_name' => 'Private'],
            ['abb_name' => 'Rec', 'full_name' => 'Recruit'],
        ];

        foreach ($ranks as $rank) {
            Rank::updateOrCreate(
                ['abb_name' => $rank['abb_name']],
                ['full_name' => $rank['full_name']]
            );
        }
    }
}

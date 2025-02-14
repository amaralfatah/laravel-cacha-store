<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run()
    {
        $units = [
            [
                'code' => 'BAL',
                'name' => 'KARUNG/SAK'
            ],
            [
                'code' => 'BND15',
                'name' => 'BANDIT 15'
            ],
            [
                'code' => 'BND4',
                'name' => 'BANDIT 4'
            ],
            [
                'code' => 'BND10',
                'name' => 'BANDIT10'
            ],
            [
                'code' => 'BND12',
                'name' => 'BANDIT12'
            ],
            [
                'code' => 'BND2',
                'name' => 'BANDIT 2'
            ],
            [
                'code' => 'BND20',
                'name' => 'BANDIT20'
            ],
            [
                'code' => 'BND24',
                'name' => 'BANDIT24'
            ],
            [
                'code' => 'BND3',
                'name' => 'BANDIT3'
            ],
            [
                'code' => 'BND30',
                'name' => 'BANDIT30'
            ],
            [
                'code' => 'BND36',
                'name' => 'BANDIT36'
            ],
            [
                'code' => 'BND5',
                'name' => 'BANDIT5'
            ],
            [
                'code' => 'BND6',
                'name' => 'BANDIT6'
            ],
            [
                'code' => 'BND72',
                'name' => 'BANDIT72'
            ],
            [
                'code' => 'BND8',
                'name' => 'BANDIT8'
            ],
            [
                'code' => 'BTL',
                'name' => 'BOTOL/TUBE'
            ],
            [
                'code' => 'KLG',
                'name' => 'KALENG/TOPLES'
            ],
            [
                'code' => 'LBR6',
                'name' => 'LEMBAR6LS'
            ],
            [
                'code' => 'PAK',
                'name' => 'PAK/BUNGKUS'
            ],
            [
                'code' => 'PCS',
                'name' => 'PICIES'
            ],
            [
                'code' => 'RTG',
                'name' => 'RENTENG'
            ],
            [
                'code' => 'BOX',
                'name' => 'BOX/DUS'
            ],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'code' => $unit['code'],
                'name' => $unit['name'],
                'is_active' => true,
            ]);
        }
    }
}

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
                'store_id' => 1,
                'code' => 'BAL',
                'name' => 'KARUNG/SAK'
            ],
            [
                'store_id' => 1,
                'code' => 'BND 15',
                'name' => 'BANDIT 15'
            ],
            [
                'store_id' => 1,
                'code' => 'BND 4',
                'name' => 'BANDIT 4'
            ],
            [
                'store_id' => 1,
                'code' => 'BND10',
                'name' => 'BANDIT10'
            ],
            [
                'store_id' => 1,
                'code' => 'BND12',
                'name' => 'BANDIT12'
            ],
            [
                'store_id' => 1,
                'code' => 'BND2',
                'name' => 'BANDIT 2'
            ],
            [
                'store_id' => 1,
                'code' => 'BND20',
                'name' => 'BANDIT20'
            ],
            [
                'store_id' => 1,
                'code' => 'BND24',
                'name' => 'BANDIT24'
            ],
            [
                'store_id' => 1,
                'code' => 'BND3',
                'name' => 'BANDIT3'
            ],
            [
                'store_id' => 1,
                'code' => 'BND30',
                'name' => 'BANDIT30'
            ],
            [
                'store_id' => 1,
                'code' => 'BND36',
                'name' => 'BANDIT36'
            ],
            [
                'store_id' => 1,
                'code' => 'BND5',
                'name' => 'BANDIT5'
            ],
            [
                'store_id' => 1,
                'code' => 'BND6',
                'name' => 'BANDIT6'
            ],
            [
                'store_id' => 1,
                'code' => 'BND72',
                'name' => 'BANDIT72'
            ],
            [
                'store_id' => 1,
                'code' => 'BND8',
                'name' => 'BANDIT8'
            ],
            [
                'store_id' => 1,
                'code' => 'BTL',
                'name' => 'BOTOL/TUBE'
            ],
            [
                'store_id' => 1,
                'code' => 'KLG',
                'name' => 'KALENG/TOPLES'
            ],
            [
                'store_id' => 1,
                'code' => 'LBR6',
                'name' => 'LEMBAR6LS'
            ],
            [
                'store_id' => 1,
                'code' => 'PAK',
                'name' => 'PAK/BUNGKUS'
            ],
            [
                'store_id' => 1,
                'code' => 'PCS',
                'name' => 'PICIES'
            ],
            [
                'store_id' => 1,
                'code' => 'RTG',
                'name' => 'RENTENG'
            ],
            [
                'store_id' => 1,
                'code' => 'BOX',
                'name' => 'BOX/DUS'
            ],
            [
                'store_id' => 1,
                'code' => '01',
                'name' => 'Undefined01'
            ],
            [
                'store_id' => 1,
                'code' => '02',
                'name' => 'Undefined02'
            ],
            [
                'store_id' => 1,
                'code' => '03',
                'name' => 'Undefined03'
            ],
            [
                'store_id' => 1,
                'code' => '04',
                'name' => 'Undefined04'
            ],
            [
                'store_id' => 1,
                'code' => '05',
                'name' => 'Undefined05'
            ],
            [
                'store_id' => 1,
                'code' => '06',
                'name' => 'Undefined06'
            ],
            [
                'store_id' => 1,
                'code' => '07',
                'name' => 'Undefined07'
            ],
            [
                'store_id' => 1,
                'code' => '08',
                'name' => 'Undefined08'
            ],
            [
                'store_id' => 1,
                'code' => '09',
                'name' => 'Undefined09'
            ],
            [
                'store_id' => 1,
                'code' => '10',
                'name' => 'Undefined10'
            ],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'store_id' => $unit['store_id'],
                'code' => $unit['code'],
                'name' => $unit['name'],
                'is_active' => true,
            ]);
        }
    }
}

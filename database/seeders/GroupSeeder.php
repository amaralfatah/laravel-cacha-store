<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $groups = [
            [
                'store_id' => 1,
                'code' => '101',
                'name' => 'MAKANAN',
            ],
            [
                'store_id' => 1,
                'code' => '202',
                'name' => 'MINUMAN',
            ],
            [
                'store_id' => 1,
                'code' => '303',
                'name' => 'ALAT ALAT',
            ],
            [
                'store_id' => 1,
                'code' => '404',
                'name' => 'VITAMIN',
            ],
            [
                'store_id' => 1,
                'code' => '505',
                'name' => 'KOSMETIK',
            ],
            [
                'store_id' => 1,
                'code' => '606',
                'name' => 'GAS',
            ],
            [
                'store_id' => 1,
                'code' => '707',
                'name' => 'OBAT',
            ],
            [
                'store_id' => 1,
                'code' => '808',
                'name' => 'RACUN',
            ],
            [
                'store_id' => 1,
                'code' => '910',
                'name' => 'BAHAN DASAR',
            ],
            [
                'store_id' => 2,
                'code' => 'PET01',
                'name' => 'HEWAN',
            ],
        ];

        foreach ($groups as $group) {
            Group::create([
                'store_id' => $group['store_id'],
                'code' => $group['code'],
                'name' => $group['name'],
                'is_active' => true,
            ]);
        }
    }
}

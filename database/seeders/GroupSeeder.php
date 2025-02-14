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
                'code' => '101',
                'name' => 'MAKANAN',
            ],
            [
                'code' => '202',
                'name' => 'MINUMAN',
            ],
            [
                'code' => '303',
                'name' => 'ALAT ALAT',
            ],
            [
                'code' => '404',
                'name' => 'VITAMIN',
            ],
            [
                'code' => '505',
                'name' => 'KOSMETIK',
            ],
            [
                'code' => '606',
                'name' => 'GAS',
            ],
            [
                'code' => '707',
                'name' => 'OBAT',
            ],
            [
                'code' => '808',
                'name' => 'RACUN',
            ],
            [
                'code' => '910',
                'name' => 'BAHAN DASAR',
            ],
        ];

        foreach ($groups as $group) {
            Group::create([
                'code' => $group['code'],
                'name' => $group['name'],
                'is_active' => true,
            ]);
        }
    }
}

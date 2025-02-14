<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'code' => '001',
                'name' => 'MAKANAN RINGAN',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '002',
                'name' => 'SEMBAKO',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '003',
                'name' => 'AIR MINERAL',
                'group_id' => 2, // 2 MINUMAN
            ],
            [
                'code' => '004',
                'name' => 'SUSU',
                'group_id' => 2, // 2 MINUMAN
            ],
            [
                'code' => '005',
                'name' => 'SOFTDRINK',
                'group_id' => 2, // 2 MINUMAN
            ],
            [
                'code' => '006',
                'name' => 'MAINAN ANAK',
                'group_id' => 3, // 3 ALAT ALAT
            ],
            [
                'code' => '007',
                'name' => 'SABUN CUCI',
                'group_id' => 4, // 4 VITAMIN
            ],
            [
                'code' => '008',
                'name' => 'SABUN MANDI',
                'group_id' => 4, // 4 VITAMIN
            ],
            [
                'code' => '009',
                'name' => 'MAKANAN INSTAN',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '010',
                'name' => 'MINUMAN INSTAN',
                'group_id' => 2, // 2 MINUMAN
            ],
            [
                'code' => '011',
                'name' => 'PERLENGKAPAN BAYI',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '012',
                'name' => 'ACCESORIES',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '013',
                'name' => 'OBAT LUAR',
                'group_id' => 7, // 7 OBAT
            ],
            [
                'code' => '014',
                'name' => 'SUPLEMEN',
                'group_id' => 2, // 2 MINUMAN
            ],
            [
                'code' => '015',
                'name' => 'ALAT MANDI',
                'group_id' => 4, // 4 VITAMIN
            ],
            [
                'code' => '016',
                'name' => 'PERLENGKAPAN RUMAH',
                'group_id' => 3, // 3 ALAT ALAT
            ],
            [
                'code' => '017',
                'name' => 'BUMBU MASAK',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '018',
                'name' => 'PEMBALUT',
                'group_id' => 3, // 3 ALAT ALAT
            ],
            [
                'code' => '019',
                'name' => 'OBAT MINUM',
                'group_id' => 7, // 7 OBAT
            ],
            [
                'code' => '020',
                'name' => 'ROKOK',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '021',
                'name' => 'SERANGGA',
                'group_id' => 8, // 8 RACUN
            ],
            [
                'code' => '022',
                'name' => 'MINYAK WANGI',
                'group_id' => 7, // 7 OBAT
            ],
            [
                'code' => '023',
                'name' => 'MAKANAN HEWAN',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '024',
                'name' => 'ALAT TULIS',
                'group_id' => 1, // 1 MAKANAN
            ],
            [
                'code' => '025',
                'name' => 'PEMBERSIH MUKA',
                'group_id' => 4, // 4 VITAMIN
            ],
            [
                'code' => '026',
                'name' => 'LAIN-LAIN',
                'group_id' => 1, // 1 MAKANAN
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'code' => $category['code'],
                'name' => $category['name'],
                'group_id' => $category['group_id'],
                'is_active' => true,
            ]);
        }
    }
}

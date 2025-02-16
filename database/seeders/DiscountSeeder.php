<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    public function run()
    {
        DB::table('discounts')->insert([
            [
                'store_id' => 1,
                'name' => 'Promo Hari Raya 10%',
                'type' => 'percentage',
                'value' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'store_id' => 1,
                'name' => 'Diskon Jum\'at Berkah',
                'type' => 'fixed',
                'value' => 5000,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'store_id' => 1,
                'name' => 'Potongan Tahun Baru',
                'type' => 'fixed',
                'value' => 2500,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

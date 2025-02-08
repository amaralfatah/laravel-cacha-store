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
                'name' => '10% Off',
                'type' => 'percentage',
                'value' => 10.00,
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Fixed Discount $50',
                'type' => 'fixed',
                'value' => 50.00,
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}

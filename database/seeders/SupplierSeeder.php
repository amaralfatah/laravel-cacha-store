<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        DB::table('suppliers')->insert([
            [
                'store_id' => 1,
                'code' => '0102',
                'name' => 'Agus Sandal Purwokerto',
                'phone' => '083456789012',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'store_id' => 2,
                'code' => 'TSK001',
                'name' => 'Grosir Snack Tasik',
                'phone' => '084567890123',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

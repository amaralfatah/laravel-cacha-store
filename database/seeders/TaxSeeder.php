<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaxSeeder extends Seeder
{
    public function run()
    {
        DB::table('taxes')->insert([
            [
                'store_id' => 1,
                'name' => 'PPN 11%',
                'rate' => 11,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'store_id' => 1,
                'name' => 'Pajak Parkir',
                'rate' => 1,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

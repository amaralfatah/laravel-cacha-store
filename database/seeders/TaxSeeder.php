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
                'name' => 'VAT',
                'rate' => 10.00,
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Income Tax',
                'rate' => 15.00,
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}

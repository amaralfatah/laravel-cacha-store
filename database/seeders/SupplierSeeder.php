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
                'name' => 'Supplier A',
                'phone' => '083456789012',
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Supplier B',
                'phone' => '084567890123',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}

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
                'name' => 'Agus Sandal Purwokerto',
                'phone' => '083456789012',
            ],
            [
                'name' => 'Grosir Snack Tasik',
                'phone' => '084567890123',
            ],
        ]);
    }
}

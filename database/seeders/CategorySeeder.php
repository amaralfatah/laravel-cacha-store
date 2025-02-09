<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            [
                'name' => 'Makanan',
                'is_active' => true,
            ],
            [
                'name' => 'Minuman',
                'is_active' => true,
            ],
            [
                'name' => 'Mainan',
                'is_active' => false,
            ],
        ]);
    }
}

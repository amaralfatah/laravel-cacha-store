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
                'name' => 'Electronics',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Furniture',
                'is_active' => true,
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}

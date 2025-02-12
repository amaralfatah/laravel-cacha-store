<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnitSeeder extends Seeder
{
    public function run()
    {
        DB::table('units')->insert([
            [
                'name' => 'Piece',
                'code' => 'PCS',
                'is_base_unit' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Box',
                'code' => 'BOX',
                'is_base_unit' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kilogram',
                'code' => 'KG',
                'is_base_unit' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

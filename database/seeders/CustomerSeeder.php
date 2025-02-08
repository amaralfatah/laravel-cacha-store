<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        DB::table('customers')->insert([
            [
                'name' => 'John Doe',
                'phone' => '081234567890',
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Jane Smith',
                'phone' => '082345678901',
                'created_at' => Carbon::now(),
            ],
        ]);
    }
}

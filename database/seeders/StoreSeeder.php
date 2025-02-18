<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    public function run()
    {
        Store::firstOrCreate(
            ['code' => 'MAIN'],
            [
                'name' => 'Toko Cacha',
                'address' => 'Emplak, Kalipucang, Pangandaran',
                'phone' => '081234567890',
                'email' => 'tokocacha@example.com',
                'is_active' => true
            ]
        );

        Store::firstOrCreate(
            ['code' => 'CMR'],
            [
                'name' => 'Cimara Jaya',
                'address' => 'Pananjung, Pangandaran, Pangandaran',
                'phone' => '081234567892',
                'email' => 'cimara@example.com',
                'is_active' => true
            ]
        );
    }
}

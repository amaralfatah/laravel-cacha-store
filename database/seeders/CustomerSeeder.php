<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        // Buat atau update customer Umum
        Customer::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Umum',
                'phone' => '-',
                'created_at' => Carbon::now(),
            ]
        );

        // Tambahkan customer lainnya
        $customers = [
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
        ];

        // Insert customer lainnya
        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                ['phone' => $customer['phone']],  // cek duplikasi berdasarkan nomor telepon
                $customer
            );
        }
    }
}

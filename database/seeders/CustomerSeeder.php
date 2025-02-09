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
            ]
        );

        // Tambahkan customer lainnya
        $customers = [
            [
                'name' => 'Amar Al Fatah',
                'phone' => '085819450001',
            ],
            [
                'name' => 'Khansa Almi\'raj',
                'phone' => '085322471629',
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

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Amar',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'store_id' => 2,
            'name' => 'User Amar',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);



        $this->call([
            StoreSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            GroupSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            DiscountSeeder::class,
            TaxSeeder::class,
            ProductSeeder::class,
            StoreCoordinatesSeeder::class
        ]);


    }
}

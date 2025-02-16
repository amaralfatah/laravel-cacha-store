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
        // User::factory(10)->create();



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
        ]);

        User::factory()->create([
            'name' => 'Amar Al Fatah',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\StoreBalance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // Get existing admin user
            $admin = User::where('role', 'admin')->first();

            if (!$admin) {
                // Create new admin user only if no admin exists
                $admin = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'admin'
                ]);
            }

            // Create main store and its balance
            $mainStore = Store::firstOrCreate(
                ['code' => 'MAIN'],
                [
                    'name' => 'Toko Cacha',
                    'address' => 'Emplak, Kalipucang, Pangandaran',
                    'phone' => '081234567890',
                    'email' => 'justlogin29@gmail.com',
                    'is_active' => true
                ]
            );

            StoreBalance::firstOrCreate(
                ['store_id' => $mainStore->id],
                [
                    'cash_amount' => 0,
                    'non_cash_amount' => 0,
                    'last_updated_by' => $admin->id
                ]
            );

            // Create Cimara store and its balance
            $cimaraStore = Store::firstOrCreate(
                ['code' => 'CMR'],
                [
                    'name' => 'Cimara Jaya',
                    'address' => 'Pananjung, Pangandaran, Pangandaran',
                    'phone' => '081234567892',
                    'email' => 'cimara@example.com',
                    'is_active' => true
                ]
            );

            StoreBalance::firstOrCreate(
                ['store_id' => $cimaraStore->id],
                [
                    'cash_amount' => 0,
                    'non_cash_amount' => 0,
                    'last_updated_by' => $admin->id
                ]
            );
        });
    }
}

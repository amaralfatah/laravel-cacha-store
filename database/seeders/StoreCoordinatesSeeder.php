<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $store = Store::find(1);

        if ($store) {
            $store->update([
                'latitude' => -7.6611617,   // Jakarta coordinates (as an example)
                'longitude' => 108.7310816  // You should replace these with actual coordinates
            ]);

            $this->command->info('Store coordinates updated successfully.');
        } else {
            $this->command->error('Store with ID 1 not found.');
        }
    }
}

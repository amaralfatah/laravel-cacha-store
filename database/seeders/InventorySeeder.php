<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();

        foreach($products as $product) {
            if($product->default_unit_id) {
                Inventory::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'unit_id' => $product->default_unit_id
                    ],
                    [
                        'quantity' => 0,
                        'min_stock' => 0
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Inventory;
use App\Models\PriceTier;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Sample products data
        $products = [
            [
                'name' => 'Mie Instan Goreng',
                'barcode' => '8998866200125',
                'category_id' => 1,
                'tax_id' => 1,
                'discount_id' => 1,
                'is_active' => true,
                'units' => [
                    [
                        'unit_id' => 1, // Unit PCS
                        'conversion_factor' => 1,
                        'purchase_price' => 2500,
                        'selling_price' => 3000,
                        'is_default' => true,
                        'inventory' => 100,
                        'min_stock' => 20,
                        'price_tiers' => [
                            ['min_quantity' => 40, 'price' => 2800],
                            ['min_quantity' => 80, 'price' => 2600],
                        ]
                    ],
                    [
                        'unit_id' => 2, // Unit DUS
                        'conversion_factor' => 40,
                        'purchase_price' => 100000,
                        'selling_price' => 115000,
                        'is_default' => false,
                        'inventory' => 5,
                        'min_stock' => 1,
                        'price_tiers' => [
                            ['min_quantity' => 2, 'price' => 110000],
                            ['min_quantity' => 5, 'price' => 105000],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Beras Premium',
                'barcode' => '8998866200126',
                'category_id' => 1,
                'tax_id' => 1,
                'discount_id' => null,
                'is_active' => true,
                'units' => [
                    [
                        'unit_id' => 1, // Unit KG
                        'conversion_factor' => 1,
                        'purchase_price' => 10000,
                        'selling_price' => 13000,
                        'is_default' => true,
                        'inventory' => 50,
                        'min_stock' => 10,
                        'price_tiers' => [
                            ['min_quantity' => 10, 'price' => 12500],
                            ['min_quantity' => 25, 'price' => 12000],
                        ]
                    ],
                    [
                        'unit_id' => 2, // Unit KARUNG
                        'conversion_factor' => 25,
                        'purchase_price' => 300000,
                        'selling_price' => 310000,
                        'is_default' => false,
                        'inventory' => 4,
                        'min_stock' => 1,
                        'price_tiers' => [
                            ['min_quantity' => 2, 'price' => 300000],
                            ['min_quantity' => 4, 'price' => 290000],
                        ]
                    ]
                ]
            ]
        ];

        foreach ($products as $productData) {
            // Create product
            $product = Product::create([
                'name' => $productData['name'],
                'barcode' => $productData['barcode'],
                'barcode_image' => null, // Will be generated by observer/trigger
                'category_id' => $productData['category_id'],
                'tax_id' => $productData['tax_id'],
                'discount_id' => $productData['discount_id'],
                'is_active' => $productData['is_active'],
            ]);

            // Create units, inventory and price tiers
            foreach ($productData['units'] as $unitData) {
                // Create product unit
                $productUnit = ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_id' => $unitData['unit_id'],
                    'conversion_factor' => $unitData['conversion_factor'],
                    'purchase_price' => $unitData['purchase_price'],
                    'selling_price' => $unitData['selling_price'],
                    'is_default' => $unitData['is_default'],
                ]);

                // Set default unit in product if this is default
                if ($unitData['is_default']) {
                    $product->update(['default_unit_id' => $unitData['unit_id']]);
                }

                // Create inventory
                Inventory::create([
                    'product_id' => $product->id,
                    'unit_id' => $unitData['unit_id'],
                    'quantity' => $unitData['inventory'],
                    'min_stock' => $unitData['min_stock'],
                ]);

                // Create price tiers
                foreach ($unitData['price_tiers'] as $tierData) {
                    PriceTier::create([
                        'product_id' => $product->id,
                        'unit_id' => $unitData['unit_id'],
                        'min_quantity' => $tierData['min_quantity'],
                        'price' => $tierData['price'],
                    ]);
                }
            }
        }
    }
}

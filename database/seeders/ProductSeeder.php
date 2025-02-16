<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Debug: Cek dulu semua unit yang tersedia
        $units = Unit::all();
        Log::info('Available units:', $units->toArray());

        // Pastikan unit ada sebelum digunakan
        $pcsUnit = Unit::where('code', 'PCS')->first();
        if (!$pcsUnit) {
            throw new \Exception('Unit PCS not found in database');
        }

        $boxUnit = Unit::where('code', 'BOX')->first();
        if (!$boxUnit) {
            throw new \Exception('Unit BOX not found in database');
        }

        $balUnit = Unit::where('code', 'BAL')->first();
        if (!$balUnit) {
            throw new \Exception('Unit BAL not found in database');
        }

        $btlUnit = Unit::where('code', 'BTL')->first();
        if (!$btlUnit) {
            throw new \Exception('Unit BTL not found in database');
        }

        $products = [
            [
                'code' => 'P001',
                'name' => 'Mie Goreng Sedap',
                'barcode' => '8995757214004',
                'store_id' => 1,
                'category_id' => 9, // MAKANAN INSTAN (00901)
                'supplier_id' => 1,
                'is_active' => true,
                'units' => [
                    [
                        'unit_id' => $pcsUnit->id,
                        'conversion_factor' => 1,
                        'purchase_price' => 2800,
                        'selling_price' => 3500,
                        'stock' => 100,
                        'min_stock' => 50,
                        'is_default' => true
                    ],
                    [
                        'unit_id' => $boxUnit->id,
                        'conversion_factor' => 40,
                        'purchase_price' => 110000,
                        'selling_price' => 135000,
                        'stock' => 10,
                        'min_stock' => 2,
                        'is_default' => false
                    ]
                ]
            ],
            // ... produk lainnya
        ];

        try {
            foreach ($products as $productData) {
                // Create product
                $product = Product::create([
                    'store_id' => $productData['store_id'],
                    'code' => $productData['code'],
                    'name' => $productData['name'],
                    'barcode' => $productData['barcode'],
                    'category_id' => $productData['category_id'],
                    'supplier_id' => $productData['supplier_id'],
                    'is_active' => $productData['is_active']
                ]);

                // Create product units
                foreach ($productData['units'] as $unitData) {
                    ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $unitData['unit_id'],
                        'conversion_factor' => $unitData['conversion_factor'],
                        'purchase_price' => $unitData['purchase_price'],
                        'selling_price' => $unitData['selling_price'],
                        'stock' => $unitData['stock'],
                        'min_stock' => $unitData['min_stock'],
                        'is_default' => $unitData['is_default']
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in ProductSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}

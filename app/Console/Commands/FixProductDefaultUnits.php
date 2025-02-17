<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixProductDefaultUnits extends Command
{
    protected $signature = 'products:fix-default-units';
    protected $description = 'Memastikan semua produk memiliki unit default';

    public function handle()
    {
        $this->info('Mulai memperbaiki unit default produk...');

        $products = Product::whereDoesntHave('productUnits', function($query) {
            $query->where('is_default', true);
        })->get();

        $this->info("Ditemukan {$products->count()} produk tanpa unit default");
        $bar = $this->output->createProgressBar($products->count());

        foreach ($products as $product) {
            DB::beginTransaction();
            try {
                // Ambil unit pertama dari produk
                $firstUnit = $product->productUnits()->first();

                if ($firstUnit) {
                    $firstUnit->update(['is_default' => true]);
                    $this->line("Set unit default untuk produk {$product->code}");
                    Log::info("Unit default diset untuk produk", [
                        'product_code' => $product->code,
                        'unit_id' => $firstUnit->unit_id
                    ]);
                } else {
                    $this->error("Produk {$product->code} tidak memiliki satuan sama sekali!");
                    Log::warning("Produk tanpa satuan ditemukan", [
                        'product_code' => $product->code
                    ]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error saat memproses produk {$product->code}: " . $e->getMessage());
                Log::error("Error saat memproses produk", [
                    'product_code' => $product->code,
                    'error' => $e->getMessage()
                ]);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai memperbaiki unit default produk');
    }
}

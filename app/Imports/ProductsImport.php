<?php

namespace App\Imports;

use App\Models\ImportLog;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\DB;

class ProductsImport implements
    ToCollection,
    WithValidation,
    WithChunkReading,
    WithBatchInserts,
    WithCustomCsvSettings,
    ShouldQueue,
    SkipsOnError,
    SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected $importLog;
    protected $categories;
    protected $suppliers;
    protected $units;
    protected $store_id;

    public function __construct($filename)
    {
        $this->store_id = auth()->user()->role === 'admin'
            ? session('selected_store_id', 1)
            : auth()->user()->store_id;

        $this->importLog = ImportLog::create([
            'filename' => $filename,
            'status' => 'pending'
        ]);

        $this->loadMasterData();
    }

    protected function loadMasterData()
    {
        $storeId = $this->store_id;

        $this->categories = Category::where('store_id', $storeId)
            ->pluck('id', 'code')
            ->toArray();

        $this->suppliers = Supplier::where('store_id', $storeId)
            ->pluck('id', 'code')
            ->toArray();

        $this->units = Unit::where('store_id', $storeId)
            ->pluck('id', 'code')
            ->toArray();

        Log::info("Data Master Dimuat:", [
            'categories' => $this->categories,
            'suppliers' => $this->suppliers,
            'units' => $this->units
        ]);
    }

    public function collection(Collection $rows)
    {
        Log::info("=== Memulai Proses Import ===");
        Log::info("ID Toko: " . $this->store_id);

        // Ambil baris header (baris pertama)
        $headers = $rows->shift()->map(function($item) {
            return strtolower(trim($item));
        })->toArray();

        Log::info("Headers yang ditemukan:", $headers);

        // Filter baris kosong
        $rows = $rows->filter(function ($row) {
            return !empty($row[0]);
        });

        Log::info("Data baris yang akan diproses:", [
            'jumlah_baris' => $rows->count(),
            'baris_pertama' => $rows->first(),
            'semua_baris' => $rows->toArray()
        ]);

        $this->importLog->update(['status' => 'processing']);

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Konversi baris menjadi array asosiatif
                $data = array_combine($headers, $row);

                Log::info("Memproses data baris:", [
                    'index' => $index,
                    'data' => $data
                ]);

                // Validasi unit
                if (!isset($this->units[$data['satuan']])) {
                    throw new \Exception("Satuan dengan kode {$data['satuan']} tidak ditemukan");
                }

                // Buat atau update produk
                $product = Product::updateOrCreate(
                    [
                        'code' => $data['kode'],
                        'store_id' => $this->store_id
                    ],
                    [
                        'name' => $data['nama'],
                        'barcode' => $data['barcode'],
                        'category_id' => $this->categories[$data['kategori']] ?? null,
                        'supplier_id' => $this->suppliers[$data['supplier']] ?? null,
                        'is_active' => true
                    ]
                );

                Log::info("Produk dibuat/diupdate:", ['product' => $product->toArray()]);

                // Buat atau update satuan produk
                $productUnit = $product->productUnits()->updateOrCreate(
                    [
                        'unit_id' => $this->units[$data['satuan']],
                        'store_id' => $this->store_id,
                        'is_default' => true
                    ],
                    [
                        'conversion_factor' => 1,
                        'purchase_price' => floatval($data['hrgbeli']),
                        'selling_price' => floatval($data['hrgjual']),
                        'stock' => floatval($data['stokawal']),
                        'min_stock' => floatval($data['stokmin'])
                    ]
                );

                Log::info("Satuan produk dibuat/diupdate:", ['product_unit' => $productUnit->toArray()]);
            }

            DB::commit();
            $this->importLog->update(['status' => 'completed']);
            Log::info("=== Import Berhasil Diselesaikan ===");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import gagal: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            $this->importLog->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required|string|max:255', // kode
            '*.1' => 'required|string|max:255', // nama
            '*.2' => 'required|string|max:100', // barcode
            '*.4' => [ // kategori
                'nullable',
                'string',
                Rule::exists('categories', 'code')->where('store_id', $this->store_id)
            ],
            '*.5' => [ // supplier
                'nullable',
                'string',
                Rule::exists('suppliers', 'code')->where('store_id', $this->store_id)
            ],
            '*.6' => [ // satuan
                'required',
                'string',
                Rule::exists('units', 'code')->where('store_id', $this->store_id)
            ]
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8',
            'delimiter' => ',',
        ];
    }
}

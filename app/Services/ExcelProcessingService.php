<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\ImportLog;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Generator;

class BatchReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow = 0;

    public function setRows($start, $end) {
        $this->startRow = $start;
        $this->endRow = $end;
    }

    public function readCell($columnAddress, $row, $worksheetName = '') {
        if ($row >= $this->startRow && $row <= $this->endRow) {
            return true;
        }
        return false;
    }
}

class ExcelProcessingService
{
    protected $categories;
    protected $suppliers;
    protected $units;
    protected $importLog;
    protected $store_id;
    protected $chunkSize = 1000;
    protected $readFilter;

    // Mapping header dari Excel ke field yang dibutuhkan
    protected $headerMapping = [
        'kode' => 'kode',
        'nama' => 'nama',
        'barcode' => 'barcode',
        'kelompok' => 'group_code',
        'kategori' => 'kategori',
        'supplier' => 'supplier',
        'satuan' => 'satuan',
        'hrgbeli' => 'hrgbeli',
        'hrgjual' => 'hrgjual',
        'stokawal' => 'stokawal',
        'stokmin' => 'stokmin'
    ];

    protected $requiredHeaders = ['kode', 'nama', 'satuan', 'hrgbeli', 'hrgjual'];


    public function __construct()
    {
        $this->readFilter = new BatchReadFilter();
    }

    public function processExcelUpload($file)
    {
        ini_set('memory_limit', '512M');

        $filename = $file->getClientOriginalName();
        $store_id = auth()->user()->role === 'admin'
            ? session('selected_store_id', 1)
            : auth()->user()->store_id;

        // Save file to storage
        $path = $file->storeAs('imports', $filename);
        $fullPath = Storage::path($path);

        // Validate Excel file first
        $fileInfo = $this->validateExcelFile($fullPath);

        // Create import log
        $importLog = ImportLog::create([
            'filename' => $filename,
            'status' => 'pending',
            'total_rows' => $fileInfo['total_rows']
        ]);

        return [
            'import_log_id' => $importLog->id,
            'file_path' => $path,
            'store_id' => $store_id,
            'total_rows' => $fileInfo['total_rows']
        ];
    }

    public function processExcelFileInQueue($filePath, $importLogId, $storeId)
    {
        ini_set('memory_limit', '512M');

        $this->store_id = $storeId;
        $this->importLog = ImportLog::findOrFail($importLogId);
        $this->loadMasterData();

        try {
            $this->importLog->update(['status' => 'processing']);

            $filePath = Storage::path($filePath);
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);

            // Baca header terlebih dahulu
            $this->readFilter->setRows(1, 1);
            $reader->setReadFilter($this->readFilter);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $headers = $this->getHeaders($worksheet);

            // Bersihkan memori
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            unset($worksheet);

            Log::info("Headers yang ditemukan:", ['headers' => $headers]);

            // Proses data dalam chunk
            $processedRows = 0;
            $startRow = 2; // Mulai dari baris 2 (setelah header)

            while ($startRow <= $this->importLog->total_rows + 1) {
                $endRow = min($startRow + $this->chunkSize - 1, $this->importLog->total_rows + 1);

                $this->readFilter->setRows($startRow, $endRow);
                $reader->setReadFilter($this->readFilter);

                $spreadsheet = $reader->load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();

                DB::beginTransaction();
                try {
                    foreach ($worksheet->getRowIterator($startRow, $endRow) as $row) {
                        $rowData = [];
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);

                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        if (!empty($rowData[0])) {
                            $this->processRow($headers, $rowData);
                            $processedRows++;

                            // Update progress setiap 100 baris
                            if ($processedRows % 100 === 0) {
                                $this->importLog->update(['processed_rows' => $processedRows]);
                            }
                        }
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }

                // Bersihkan memori
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
                unset($worksheet);
                gc_collect_cycles();

                $startRow += $this->chunkSize;
            }

            $this->importLog->update([
                'status' => 'completed',
                'processed_rows' => $processedRows
            ]);

            // Hapus file temporary
            Storage::delete($filePath);

        } catch (\Exception $e) {
            Log::error("Error saat memproses file:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->importLog->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    protected function getHeaders(Worksheet $worksheet): array
    {
        $headerRow = $worksheet->getRowIterator(1)->current();
        $cellIterator = $headerRow->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $headers = [];
        foreach ($cellIterator as $cell) {
            $value = $cell->getValue();
            if ($value !== null) {
                $headers[] = strtolower(trim($value));
            }
        }

        return $headers;
    }

    protected function getRowChunks(Worksheet $worksheet, array $headers): Generator
    {
        $rows = [];
        $count = 0;
        $headerCount = count($headers);

        // Mulai dari baris 2 (setelah header)
        $rowIterator = $worksheet->getRowIterator(2);

        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Pastikan jumlah kolom sama dengan header
            if (count($rowData) < $headerCount) {
                $rowData = array_pad($rowData, $headerCount, null);
            } elseif (count($rowData) > $headerCount) {
                $rowData = array_slice($rowData, 0, $headerCount);
            }

            // Skip baris kosong
            if (empty($rowData[0])) {
                continue;
            }

            $rows[] = $rowData;
            $count++;

            if ($count >= $this->chunkSize) {
                yield $rows;
                $rows = [];
                $count = 0;
            }
        }

        if (!empty($rows)) {
            yield $rows;
        }
    }

    protected function processRow($headers, $rowData)
    {
        try {
            // Log raw data untuk debugging
            Log::info("Processing row with data:", [
                'headers_count' => count($headers),
                'values_count' => count($rowData),
                'headers' => $headers,
                'values' => $rowData
            ]);

            // Pastikan jumlah kolom sama
            if (count($rowData) < count($headers)) {
                $rowData = array_pad($rowData, count($headers), null);
            } else if (count($rowData) > count($headers)) {
                $rowData = array_slice($rowData, 0, count($headers));
            }

            // Combine headers dengan data
            $data = array_combine($headers, $rowData);
            $data = $this->cleanData($data);

            // Skip jika kode kosong
            if (empty($data['kode'])) {
                Log::info("Skipping row with empty code");
                return false;
            }

            Log::info("Mapped row data:", ['data' => $data]);

            // Validate required data
            $this->validateRowData($data);

            DB::beginTransaction();
            try {
                // Get or create the unit
                $unit = $this->getOrCreateUnit($data['satuan']);
                Log::info("Unit processed:", ['unit' => $unit->toArray()]);

                // Create or update product
                $productData = [
                    'name' => $data['nama'],
                    'barcode' => $data['barcode'] ?? null,
                    'is_active' => true,
                    'store_id' => $this->store_id
                ];

                // Add category if exists
                if (!empty($data['kategori'])) {
                    $productData['category_id'] = $this->categories[$data['kategori']] ?? null;
                }

                // Add supplier if exists
                if (!empty($data['supplier'])) {
                    $productData['supplier_id'] = $this->suppliers[$data['supplier']] ?? null;
                }

                Log::info("Creating/updating product with data:", [
                    'code' => $data['kode'],
                    'data' => $productData
                ]);

                $product = Product::updateOrCreate(
                    [
                        'code' => $data['kode'],
                        'store_id' => $this->store_id
                    ],
                    $productData
                );

                Log::info("Product processed:", ['product' => $product->toArray()]);

                // Create or update product unit
                $productUnitData = [
                    'unit_id' => $unit->id,
                    'store_id' => $this->store_id,
                    'conversion_factor' => 1,
                    'purchase_price' => $this->parseNumber($data['hrgbeli']),
                    'selling_price' => $this->parseNumber($data['hrgjual']),
                    'stock' => $this->parseNumber($data['stokawal'] ?? 0),
                    'min_stock' => $this->parseNumber($data['stokmin'] ?? 0),
                    'is_default' => true
                ];

                Log::info("Creating/updating product unit with data:", ['data' => $productUnitData]);

                $productUnit = $product->productUnits()->updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'store_id' => $this->store_id
                    ],
                    $productUnitData
                );

                Log::info("Product unit processed:", ['product_unit' => $productUnit->toArray()]);

                DB::commit();
                return true;

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Database transaction failed:", [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("Row processing failed:", [
                'data' => $data ?? [],
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function cleanData($data)
    {
        $defaults = [
            'kode' => null,           // No default as it's required
            'nama' => null,           // No default as it's required
            'barcode' => null,        // Optional field
            'kategori' => null,       // Optional field
            'supplier' => null,       // Optional field
            'satuan' => null,         // No default as it's required
            'hrgbeli' => 0,          // Default purchase price is 0
            'hrgjual' => 0,          // Default selling price is 0
            'stokawal' => 0,         // Default initial stock is 0
            'stokmin' => 0,          // Default minimum stock is 0
            'group_code' => null     // Optional field
        ];

        $cleaned = [];
        foreach ($data as $key => $value) {
            // Clean whitespace if string
            if (is_string($value)) {
                $value = trim($value);
            }

            // Handle null, empty string, or whitespace-only string
            if ($value === '' || $value === null || (is_string($value) && trim($value) === '')) {
                $cleaned[$key] = $defaults[$key] ?? null;
                continue;
            }

            // Remove special characters from code
            if ($key === 'kode') {
                $value = preg_replace('/[^A-Za-z0-9-_]/', '', $value);
            }

            $cleaned[$key] = $value;
        }

        // Ensure all default fields are set even if they weren't in the input
        foreach ($defaults as $key => $defaultValue) {
            if (!isset($cleaned[$key])) {
                $cleaned[$key] = $defaultValue;
            }
        }

        return $cleaned;
    }

    protected function parseNumber($value)
    {
        if (empty($value) || $value === null || $value === '') {
            return 0;
        }

        // Jika value adalah string, bersihkan format
        if (is_string($value)) {
            // Hapus semua karakter kecuali angka dan tanda desimal
            $value = preg_replace('/[^0-9.]/', '', $value);
        }

        return (float) $value;
    }

    protected function updateOtherUnits($product, $defaultUnit, $purchasePrice, $sellingPrice, $stock)
    {
        $otherUnits = $product->productUnits()
            ->where('id', '!=', $defaultUnit->id)
            ->get();

        Log::info("Updating other units:", [
            'product_id' => $product->id,
            'default_unit_id' => $defaultUnit->id,
            'other_units_count' => $otherUnits->count()
        ]);

        foreach ($otherUnits as $otherUnit) {
            $updateData = [
                'purchase_price' => $purchasePrice * $otherUnit->conversion_factor,
                'selling_price' => $sellingPrice * $otherUnit->conversion_factor,
                'stock' => $this->convertStock($stock, 1, $otherUnit->conversion_factor)
            ];

            Log::info("Updating unit:", [
                'unit_id' => $otherUnit->id,
                'update_data' => $updateData
            ]);

            $otherUnit->update($updateData);
        }
    }

    protected function ensureProductHasDefaultUnit($product)
    {
        $hasDefaultUnit = $product->productUnits()
            ->where('is_default', true)
            ->exists();

        if (!$hasDefaultUnit) {
            $firstUnit = $product->productUnits()->first();
            if ($firstUnit) {
                $firstUnit->update(['is_default' => true]);
                Log::info("Set default unit for product", [
                    'product_code' => $product->code,
                    'unit_id' => $firstUnit->unit_id
                ]);
            } else {
                throw new \Exception("Product {$product->code} has no units at all");
            }
        }
    }

    protected function loadMasterData()
    {
        $this->categories = Category::where('store_id', $this->store_id)
            ->pluck('id', 'code')
            ->toArray();

        $this->suppliers = Supplier::where('store_id', $this->store_id)
            ->pluck('id', 'code')
            ->toArray();

        $this->units = Unit::where('store_id', $this->store_id)
            ->pluck('id', 'code')
            ->toArray();

        Log::info("Data master dimuat:", [
            'categories' => $this->categories,
            'suppliers' => $this->suppliers,
            'units' => $this->units
        ]);
    }

    protected function validateRowData($data)
    {
        $errors = [];

        // Validasi field wajib
        foreach ($this->requiredHeaders as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field {$field} harus diisi";
            }
        }

        // Validasi referensi ke data master
        if (!empty($data['satuan']) && !isset($this->units[$data['satuan']])) {
            $errors[] = "Satuan '{$data['satuan']}' tidak ditemukan";
        }

        if (!empty($data['kategori']) && !isset($this->categories[$data['kategori']])) {
            $errors[] = "Kategori '{$data['kategori']}' tidak ditemukan";
        }

        if (!empty($data['supplier']) && !isset($this->suppliers[$data['supplier']])) {
            $errors[] = "Supplier '{$data['supplier']}' tidak ditemukan";
        }

        // Validasi format numerik
        if (!empty($data['hrgbeli']) && !is_numeric($this->parseNumber($data['hrgbeli']))) {
            $errors[] = "Harga beli harus berupa angka";
        }

        if (!empty($data['hrgjual']) && !is_numeric($this->parseNumber($data['hrgjual']))) {
            $errors[] = "Harga jual harus berupa angka";
        }

        if (!empty($data['stokawal']) && !is_numeric($this->parseNumber($data['stokawal']))) {
            $errors[] = "Stok awal harus berupa angka";
        }

        if (!empty($data['stokmin']) && !is_numeric($this->parseNumber($data['stokmin']))) {
            $errors[] = "Stok minimal harus berupa angka";
        }

        if (!empty($errors)) {
            Log::error("Validation errors:", ['errors' => $errors]);
            throw new \Exception(implode(", ", $errors));
        }
    }

    protected function validateExcelFile($filePath)
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        try {
            // Validasi file bisa dibaca
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Cek jumlah baris
            $totalRows = $worksheet->getHighestRow();
            Log::info("Validating Excel file:", [
                'total_rows' => $totalRows,
                'highest_column' => $worksheet->getHighestColumn()
            ]);

            if ($totalRows <= 1) {
                throw new \Exception("File Excel tidak memiliki data");
            }

            // Cek header
            $headers = $this->getHeaders($worksheet);
            $missingHeaders = array_diff($this->requiredHeaders, $headers);

            if (!empty($missingHeaders)) {
                throw new \Exception('Kolom wajib tidak ditemukan: ' . implode(', ', $missingHeaders));
            }

            // Cek sample data baris pertama
            $row = $worksheet->getRowIterator(2)->current();
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $firstRowData = [];
            foreach ($cellIterator as $cell) {
                $firstRowData[] = $cell->getValue();
            }

            Log::info("Sample first row data:", ['data' => $firstRowData]);

            return [
                'total_rows' => $totalRows - 1, // Kurangi 1 untuk header
                'headers' => $headers
            ];

        } catch (\Exception $e) {
            Log::error("Excel validation failed:", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } finally {
            if (isset($spreadsheet)) {
                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
                unset($worksheet);
            }
        }
    }

    protected function getOrCreateUnit($unitCode)
    {
        if (!isset($this->units[$unitCode])) {
            // Create new unit if it doesn't exist
            $unit = Unit::create([
                'store_id' => $this->store_id,
                'code' => $unitCode,
                'name' => strtoupper($unitCode),
                'is_base_unit' => true,
                'is_active' => true
            ]);
            $this->units[$unitCode] = $unit->id;
            return $unit;
        }
        return Unit::find($this->units[$unitCode]);
    }


    protected function convertStock($quantity, $fromFactor, $toFactor)
    {
        if ($fromFactor === $toFactor) {
            return $quantity;
        }

        // Konversi ke unit default terlebih dahulu
        $inDefaultUnit = $quantity * $fromFactor;

        // Kemudian konversi ke unit tujuan
        return $inDefaultUnit / $toFactor;
    }
}

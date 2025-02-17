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
    protected $requiredHeaders = ['kode', 'nama', 'satuan', 'hrgbeli', 'hrgjual', 'stokawal', 'stokmin'];

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

        // Create lightweight reader for row counting
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        // Validate headers first
        $this->readFilter->setRows(1, 1);
        $reader->setReadFilter($this->readFilter);
        $spreadsheet = $reader->load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $headers = $this->getHeaders($worksheet);

        // Validate required headers
        $missingHeaders = array_diff($this->requiredHeaders, $headers);
        if (!empty($missingHeaders)) {
            throw new \Exception('Kolom wajib tidak ditemukan: ' . implode(', ', $missingHeaders));
        }

        $totalRows = $worksheet->getHighestRow() - 1;
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        // Create import log
        $importLog = ImportLog::create([
            'filename' => $filename,
            'status' => 'pending',
            'total_rows' => $totalRows
        ]);

        return [
            'import_log_id' => $importLog->id,
            'file_path' => $path,
            'store_id' => $store_id,
            'total_rows' => $totalRows
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
            $data = array_combine($headers, $rowData);
            $data = $this->cleanData($data);

            Log::info("Processing data:", ['data' => $data]);

            // Validate required data
            $this->validateRowData($data);

            DB::beginTransaction();
            try {
                // Get or create the unit first
                $unit = $this->getOrCreateUnit($data['satuan']);

                // Create or update product
                $product = Product::updateOrCreate(
                    [
                        'code' => $data['kode'],
                        'store_id' => $this->store_id
                    ],
                    [
                        'name' => $data['nama'],
                        'barcode' => $data['barcode'] ?? null,
                        'category_id' => $this->categories[$data['kategori']] ?? null,
                        'supplier_id' => $this->suppliers[$data['supplier']] ?? null,
                        'is_active' => true,
                        'default_unit_id' => $unit->id // Set default unit ID langsung di produk
                    ]
                );

                // Check if product already has units
                $existingUnits = $product->productUnits;
                $isFirstUnit = $existingUnits->isEmpty();

                // Create or update product unit
                $productUnit = $product->productUnits()->updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'store_id' => $this->store_id
                    ],
                    [
                        'conversion_factor' => 1.0000, // Selalu 1 untuk unit default
                        'purchase_price' => $this->parseNumber($data['hrgbeli']),
                        'selling_price' => $this->parseNumber($data['hrgjual']),
                        'stock' => $this->parseNumber($data['stokawal']),
                        'min_stock' => $this->parseNumber($data['stokmin']),
                        'is_default' => true
                    ]
                );

                // If this is not the first unit, we need to handle conversions for other units
                if (!$isFirstUnit) {
                    $this->updateOtherUnits(
                        $product,
                        $productUnit,
                        $this->parseNumber($data['hrgbeli']),
                        $this->parseNumber($data['hrgjual']),
                        $this->parseNumber($data['stokawal'])
                    );
                }

                // Make sure other units are set to non-default
                $product->productUnits()
                    ->where('id', '!=', $productUnit->id)
                    ->update(['is_default' => false]);

                DB::commit();
                return true;

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("Error processing row:", [
                'data' => $data ?? [],
                'error' => $e->getMessage()
            ]);
            throw $e;
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

        if (empty($data['kode'])) {
            $errors[] = "Kode produk harus diisi";
        }

        if (empty($data['nama'])) {
            $errors[] = "Nama produk harus diisi";
        }

        if (empty($data['satuan'])) {
            $errors[] = "Satuan harus diisi";
        } elseif (!isset($this->units[$data['satuan']])) {
            $errors[] = "Satuan '{$data['satuan']}' tidak ditemukan";
        }

        // Validasi format data
        if (empty($data['hrgbeli']) || !is_numeric($this->parseNumber($data['hrgbeli']))) {
            $errors[] = "Harga beli harus berupa angka";
        }

        if (empty($data['hrgjual']) || !is_numeric($this->parseNumber($data['hrgjual']))) {
            $errors[] = "Harga jual harus berupa angka";
        }

        if (!empty($errors)) {
            throw new \Exception(implode(", ", $errors));
        }
    }

    protected function cleanData($data)
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            // Bersihkan whitespace
            $value = trim($value);

            // Konversi ke string jika bukan null
            if ($value !== null) {
                $value = (string) $value;
            }

            // Hapus karakter khusus dari kode
            if ($key === 'kode') {
                $value = preg_replace('/[^A-Za-z0-9]/', '', $value);
            }

            $cleaned[$key] = $value;
        }
        return $cleaned;
    }

    protected function parseNumber($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Bersihkan format angka
        $value = str_replace([',', '.'], '', $value);
        return floatval($value);
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

    protected function updateOtherUnits($product, $defaultUnit, $purchasePrice, $sellingPrice, $stock)
    {
        $otherUnits = $product->productUnits()
            ->where('id', '!=', $defaultUnit->id)
            ->get();

        foreach ($otherUnits as $otherUnit) {
            $otherUnit->update([
                'purchase_price' => $purchasePrice * $otherUnit->conversion_factor,
                'selling_price' => $sellingPrice * $otherUnit->conversion_factor,
                'stock' => $this->convertStock($stock, 1, $otherUnit->conversion_factor)
            ]);
        }
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

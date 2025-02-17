<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProductImport;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\ExcelProcessingService;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ProductImportController extends Controller
{
    protected $excelService;

    protected $requiredColumns = [
        'kode' => 'Kode Produk',
        'nama' => 'Nama Produk',
        'satuan' => 'Satuan',
        'hrgbeli' => 'Harga Beli',
        'hrgjual' => 'Harga Jual'
    ];

    protected $optionalColumns = [
        'barcode' => 'Barcode',
        'kategori' => 'Kategori',
        'supplier' => 'Supplier',
        'stokawal' => 'Stok Awal',
        'stokmin' => 'Stok Minimal'
    ];

    public function __construct(ExcelProcessingService $excelService)
    {
        $this->excelService = $excelService;
    }

    public function showImportForm()
    {
        $lastImport = ImportLog::latest()->first();
        return view('products.import', compact('lastImport'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:51200' // 50MB limit
        ], [
            'file.required' => 'File Excel harus diupload',
            'file.mimes' => 'File harus berformat Excel (xlsx atau xls)',
            'file.max' => 'Ukuran file maksimal 50MB'
        ]);

        try {
            $file = $request->file('file');

            Log::info("Detail File:", [
                'nama' => $file->getClientOriginalName(),
                'ukuran' => $file->getSize(),
                'tipe' => $file->getMimeType()
            ]);

            if (!$file->isReadable()) {
                throw new \Exception("File tidak dapat dibaca");
            }

            // Proses upload dan dapatkan informasi untuk job
            $importInfo = $this->excelService->processExcelUpload($file);

            // Dispatch job ke queue
            ProcessProductImport::dispatch(
                $importInfo['file_path'],
                $importInfo['import_log_id'],
                $importInfo['store_id']
            );

            return redirect()->route('products.import.form')
                ->with('success', 'File sedang diproses. Silakan refresh halaman untuk melihat status terbaru.');

        } catch (\Exception $e) {
            Log::error("Error Import:", [
                'pesan' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('products.import.form')
                ->with('error', 'Gagal import produk: ' . $e->getMessage());
        }
    }

    public function checkStatus($importId)
    {
        $import = ImportLog::findOrFail($importId);

        return response()->json([
            'status' => $import->status,
            'processed_rows' => $import->processed_rows,
            'total_rows' => $import->total_rows,
            'error' => $import->error
        ]);
    }

    public function downloadTemplate()
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Get master data for dropdown lists
        $store_id = auth()->user()->role === 'admin'
            ? session('selected_store_id', 1)
            : auth()->user()->store_id;

        $categories = Category::where('store_id', $store_id)
            ->pluck('code')
            ->implode(',');
        $suppliers = Supplier::where('store_id', $store_id)
            ->pluck('code')
            ->implode(',');
        $units = Unit::where('store_id', $store_id)
            ->pluck('code')
            ->implode(',');

        // Set column headers
        $columns = array_merge($this->requiredColumns, $this->optionalColumns);
        $col = 'A';
        foreach ($columns as $key => $header) {
            // Set cell value
            $sheet->setCellValue($col . '1', $header);

            // Style header
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '4B5563']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ]
            ]);

            // Add note for required columns using rich text
            if (isset($this->requiredColumns[$key])) {
                $sheet->getStyle($col . '1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('CC0000');
            }

            // Set column width
            $sheet->getColumnDimension($col)->setWidth(20);

            $col++;
        }

        // Add data validation for master data columns
        if ($categories) {
            $categoryColumn = array_search('kategori', array_keys($columns)) + 1;
            $colLetter = chr(64 + $categoryColumn);
            $validation = $sheet->getDataValidation($colLetter . '2:' . $colLetter . '1000');
            $validation->setType(DataValidation::TYPE_LIST)
                ->setErrorStyle(DataValidation::STYLE_INFORMATION)
                ->setAllowBlank(true)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setFormula1('"' . $categories . '"');
        }

        if ($suppliers) {
            $supplierColumn = array_search('supplier', array_keys($columns)) + 1;
            $colLetter = chr(64 + $supplierColumn);
            $validation = $sheet->getDataValidation($colLetter . '2:' . $colLetter . '1000');
            $validation->setType(DataValidation::TYPE_LIST)
                ->setErrorStyle(DataValidation::STYLE_INFORMATION)
                ->setAllowBlank(true)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setFormula1('"' . $suppliers . '"');
        }

        if ($units) {
            $unitColumn = array_search('satuan', array_keys($columns)) + 1;
            $colLetter = chr(64 + $unitColumn);
            $validation = $sheet->getDataValidation($colLetter . '2:' . $colLetter . '1000');
            $validation->setType(DataValidation::TYPE_LIST)
                ->setErrorStyle(DataValidation::STYLE_INFORMATION)
                ->setAllowBlank(true)
                ->setShowInputMessage(true)
                ->setShowErrorMessage(true)
                ->setShowDropDown(true)
                ->setFormula1('"' . $units . '"');
        }

        // Add example data
        $exampleData = [
            [
                'PRD001',           // kode
                'Produk Contoh',    // nama
                'PCS',              // satuan
                10000,              // hrgbeli
                15000,              // hrgjual
                '8997123456789',    // barcode
                'KTG001',           // kategori
                'SUP001',           // supplier
                100,                // stokawal
                10                  // stokmin
            ]
        ];

        $row = 2;
        foreach ($exampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Create instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');
        $instructions = [
            ['Petunjuk Pengisian Template:'],
            [''],
            ['1. Kolom dengan tanda * (merah) adalah kolom wajib yang harus diisi'],
            ['2. Kode Produk tidak boleh mengandung karakter khusus'],
            ['3. Satuan harus sesuai dengan master data yang tersedia'],
            ['4. Kategori dan Supplier harus sesuai dengan kode yang tersedia'],
            ['5. Harga dan stok harus berupa angka'],
            ['6. Barcode bersifat opsional'],
            [''],
            ['Data Master yang Tersedia:'],
            [''],
            ['Kategori:', $categories],
            ['Supplier:', $suppliers],
            ['Satuan:', $units]
        ];

        $row = 1;
        foreach ($instructions as $instruction) {
            $instructionSheet->fromArray($instruction, null, 'A' . $row);
            $row++;
        }

        $instructionSheet->getColumnDimension('A')->setWidth(30);
        $instructionSheet->getColumnDimension('B')->setWidth(50);

        // Switch back to first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Create Excel file
        $writer = new Xlsx($spreadsheet);

        // Set response headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_produk.xlsx"');
        header('Cache-Control: max-age=0');

        // Save file to output
        $writer->save('php://output');
        exit;
    }
}

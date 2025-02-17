<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessProductImport;
use App\Services\ExcelProcessingService;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductImportController extends Controller
{
    protected $excelService;

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
}

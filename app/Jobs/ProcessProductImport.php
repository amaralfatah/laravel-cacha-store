<?php

namespace App\Jobs;

use App\Services\ExcelProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $importLogId;
    protected $storeId;
    public $timeout = 3600; // 1 jam timeout
    public $tries = 3; // Jumlah percobaan jika gagal

    public function __construct($filePath, $importLogId, $storeId)
    {
        $this->filePath = $filePath;
        $this->importLogId = $importLogId;
        $this->storeId = $storeId;
    }

    public function handle(ExcelProcessingService $excelService)
    {
        Log::info("Memulai proses import dalam queue", [
            'file' => $this->filePath,
            'import_log_id' => $this->importLogId
        ]);

        $excelService->processExcelFileInQueue(
            $this->filePath,
            $this->importLogId,
            $this->storeId
        );
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Import job gagal", [
            'file' => $this->filePath,
            'error' => $exception->getMessage()
        ]);
    }
}

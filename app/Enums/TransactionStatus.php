<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case RETURNED = 'returned'; // Ganti dari VOID ke RETURNED

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Draft',
            self::SUCCESS => 'Selesai',
            self::FAILED => 'Gagal',
            self::CANCELLED => 'Dibatalkan',
            self::RETURNED => 'Dikembalikan' // Update label
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::PENDING => '<span class="badge bg-warning">Draft</span>',
            self::SUCCESS => '<span class="badge bg-success">Selesai</span>',
            self::FAILED => '<span class="badge bg-danger">Gagal</span>',
            self::CANCELLED => '<span class="badge bg-secondary">Dibatalkan</span>',
            self::RETURNED => '<span class="badge bg-danger">Dikembalikan</span>' // Update badge
        };
    }
}

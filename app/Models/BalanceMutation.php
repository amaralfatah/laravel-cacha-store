<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BalanceMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'type',
        'payment_method',
        'amount',
        'previous_balance',
        'current_balance',
        'source_type',
        'source_id',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    /**
     * Get the user who created the mutation
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the source model of the mutation
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function source()
    {
        return $this->morphTo();
    }

    protected $appends = ['source_label'];

    // Accessor untuk menampilkan source type yang ramah user
    public function getSourceLabelAttribute()
    {
        $typeMap = [
            'App\Models\Transaction' => 'Transaksi Penjualan',
            'App\Models\Purchase' => 'Transaksi Pembelian',
            // Tambahkan mapping lainnya sesuai kebutuhan
        ];

        return $typeMap[$this->source_type] ?? 'Lainnya';
    }
}

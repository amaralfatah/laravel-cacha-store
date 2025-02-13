<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dengan nama model
    protected $table = 'stock_histories';

    // Tentukan kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'product_unit_id',
        'type',
        'quantity',
        'remaining_stock',
        'notes',
    ];

    // Tentukan tipe data untuk atribut tertentu jika diperlukan
    protected $casts = [
        'quantity' => 'decimal:2',
        'remaining_stock' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan model ProductUnit
    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
}

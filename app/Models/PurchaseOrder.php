<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'store_id',
        'supplier_id',
        'tax_id',
        'discount_id',
        'invoice_number',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'final_amount',
        'payment_type',
        'reference_number',
        'status',
        'purchase_date',
        'notes'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}


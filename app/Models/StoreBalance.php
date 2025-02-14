<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreBalance extends Model
{
    protected $fillable = [
        'cash_amount',
        'non_cash_amount',
        'last_updated_by'
    ];

    protected $casts = [
        'cash_amount' => 'decimal:2',
        'non_cash_amount' => 'decimal:2',
    ];

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
}

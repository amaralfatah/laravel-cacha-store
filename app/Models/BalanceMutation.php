<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'source_type',
        'source_id',
        'previous_balance',
        'current_balance',
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
    public function source()
    {
        return $this->morphTo();
    }
}

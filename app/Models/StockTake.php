<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTake extends Model
{
    protected $fillable = ['date', 'status', 'notes', 'created_by'];

    public function items()
    {
        return $this->hasMany(StockTakeItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

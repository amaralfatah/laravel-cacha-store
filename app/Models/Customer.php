<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'store_id',];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

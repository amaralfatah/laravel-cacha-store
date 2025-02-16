<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Discount extends Model
{
    protected $fillable = ['store_id','name', 'type', 'value', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

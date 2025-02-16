<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['store_id', 'code', 'name', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    // app/Models/Group.php
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'code',
        'name',
        'group_id',
        'store_id',
        'is_active'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}

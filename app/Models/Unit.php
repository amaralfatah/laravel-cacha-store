<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_base_unit',
        'is_active'
    ];

    protected $casts = [
        'is_base_unit' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }
}

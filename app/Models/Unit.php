<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'code', 'is_base_unit'];

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }
}

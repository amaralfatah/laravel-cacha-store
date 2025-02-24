<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    protected $fillable = ['store_id', 'paper_size', 'printer_name', 'auto_print'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

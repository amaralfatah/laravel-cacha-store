<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = ['filename', 'status', 'error', 'total_rows', 'processed_rows'];
}

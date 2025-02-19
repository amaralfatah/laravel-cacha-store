<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'email',
        'message',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the store that this message belongs to
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

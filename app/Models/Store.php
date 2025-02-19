<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'name',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'logo',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    // Relasi dengan User
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi dengan Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relasi dengan Customers
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    // Relasi dengan Transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function storeBalance()
    {
        return $this->hasOne(StoreBalance::class);
    }
}

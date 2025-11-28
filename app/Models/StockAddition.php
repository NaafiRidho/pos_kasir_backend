<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAddition extends Model
{
    use HasFactory;

    protected $table = 'stock_additions';
    protected $primaryKey = 'stock_addition_id';

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'notes',
        'added_at'
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    // Relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

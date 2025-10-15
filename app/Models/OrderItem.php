<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'item_number',
        'fabric',
        'color',
        'collar',
        'model',
        'detail',
        'print_type',
        'print_desc',
        'art_name',
        'cover_image',
        'sizes',
        'quantity',
        'unit_price',
        'total_price',
        'art_notes',
    ];

    protected $casts = [
        'sizes' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function sublimations()
    {
        return $this->hasMany(OrderSublimation::class, 'order_item_id');
    }

    public function files()
    {
        return $this->hasMany(OrderFile::class, 'order_item_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSublimation extends Model
{
    protected $fillable = [
        'order_item_id',
        'application_type',
        'art_name',
        'size_id',
        'size_name',
        'location_id',
        'location_name',
        'quantity',
        'color_count',
        'has_neon',
        'neon_surcharge',
        'unit_price',
        'discount_percent',
        'final_price',
        'application_image',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'final_price' => 'decimal:2',
        'neon_surcharge' => 'decimal:2',
        'has_neon' => 'boolean',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function size()
    {
        return $this->belongsTo(SublimationSize::class, 'size_id');
    }

    public function location()
    {
        return $this->belongsTo(SublimationLocation::class, 'location_id');
    }

    public function files()
    {
        return $this->hasMany(OrderSublimationFile::class, 'order_sublimation_id');
    }
}

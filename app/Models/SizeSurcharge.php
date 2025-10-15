<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeSurcharge extends Model
{
    protected $fillable = [
        'size',
        'price_from',
        'price_to',
        'surcharge',
    ];

    protected $casts = [
        'price_from' => 'decimal:2',
        'price_to' => 'decimal:2',
        'surcharge' => 'decimal:2',
    ];

    public static function getSurchargeForSize($size, $totalPrice)
    {
        return self::where('size', $size)
            ->where('price_from', '<=', $totalPrice)
            ->where(function($query) use ($totalPrice) {
                $query->whereNull('price_to')
                      ->orWhere('price_to', '>=', $totalPrice);
            })
            ->first();
    }
}

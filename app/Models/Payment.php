<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'method',
        'payment_method',
        'payment_methods',
        'amount',
        'entry_amount',
        'remaining_amount',
        'due_date',
        'payment_date',
        'entry_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'entry_date' => 'date',
        'payment_methods' => 'array',
        'amount' => 'decimal:2',
        'entry_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

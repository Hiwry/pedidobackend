<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'contract_type',
        'order_date',
        'delivery_date',
        'seller',
        'nt',
        'status_id',
        'total_items',
        'subtotal',
        'discount',
        'delivery_fee',
        'total',
        'notes',
        'cover_image',
        'is_draft',
        'client_token',
        'client_confirmed',
        'client_confirmed_at',
        'client_confirmation_notes',
        'is_editing',
        'edit_requested_at',
        'edit_notes',
        'edit_completed_at',
        'edit_status',
        'edit_approved_at',
        'edit_rejected_at',
        'edit_rejection_reason',
        'edit_approved_by',
        'is_modified',
        'last_modified_at',
        'is_cancelled',
        'cancelled_at',
        'cancellation_reason',
        'has_pending_edit',
        'has_pending_cancellation',
        'last_updated_at',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'client_confirmed' => 'boolean',
        'client_confirmed_at' => 'datetime',
        'is_editing' => 'boolean',
        'edit_requested_at' => 'datetime',
        'edit_completed_at' => 'datetime',
        'edit_approved_at' => 'datetime',
        'edit_rejected_at' => 'datetime',
        'is_modified' => 'boolean',
        'last_modified_at' => 'datetime',
        'is_cancelled' => 'boolean',
        'cancelled_at' => 'datetime',
        'has_pending_edit' => 'boolean',
        'has_pending_cancellation' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor para retornar o nome do vendedor automaticamente
     * Se o campo seller estiver vazio, retorna o nome do usuário que criou o pedido
     */
    public function getSellerAttribute($value)
    {
        if (empty($value) && $this->user) {
            return $this->user->name;
        }
        return $value;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(OrderComment::class)->orderBy('created_at', 'desc');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class)->orderBy('created_at', 'desc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function editHistory(): HasMany
    {
        return $this->hasMany(OrderEditHistory::class);
    }

    public function editApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edit_approved_by');
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function deliveryRequests(): HasMany
    {
        return $this->hasMany(DeliveryRequest::class);
    }

    public function pendingDeliveryRequest()
    {
        return $this->hasOne(DeliveryRequest::class)->where('status', 'pendente');
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(OrderCancellation::class)->orderBy('created_at', 'desc');
    }

    public function editRequests(): HasMany
    {
        return $this->hasMany(OrderEditRequest::class)->orderBy('created_at', 'desc');
    }

    public function pendingCancellation()
    {
        return $this->hasOne(OrderCancellation::class)->where('status', 'pending');
    }

    public function pendingEditRequest()
    {
        return $this->hasOne(OrderEditRequest::class)->where('status', 'pending');
    }

    public function isCancelled(): bool
    {
        return $this->is_cancelled;
    }

    public function hasPendingCancellation(): bool
    {
        return $this->has_pending_cancellation;
    }

    public function hasPendingEdit(): bool
    {
        return $this->has_pending_edit;
    }
}

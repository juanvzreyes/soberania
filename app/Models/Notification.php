<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    const TYPE_ORDER_CONFIRMATION = 'order_confirmation';
    const TYPE_DELIVERY_PENDING = 'delivery_pending';
    const TYPE_ORDER_STATUS_CHANGED = 'order_status_changed';
    const TYPE_PAYMENT_CONFIRMED = 'payment_confirmed';
    const TYPE_DELIVERY_COMPLETED = 'delivery_completed';
    const TYPE_LOW_STOCK = 'low_stock';
    const TYPE_NEW_PRODUCT = 'new_product';
    const TYPE_NEW_CATEGORY = 'new_category';
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'Pendiente';
    const STATUS_PROCESSING = 'En preparación';
    const STATUS_SHIPPING = 'En camino';
    const STATUS_DELIVERED = 'Entregado';
    const STATUS_CANCELED = 'Cancelado';

    protected $fillable = [
        'consumer_id',
        'cooperative_id',
        'total_amount',
        'status',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => 'string',
        'cancelled_at' => 'datetime',
    ];

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PROCESSING => 'En preparación',
            self::STATUS_SHIPPING => 'En camino',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELED => 'Cancelado',
        ];
    }
    
    public function consumer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consumer_id');
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cooperative_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }
}
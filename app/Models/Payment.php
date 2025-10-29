<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'Pendiente';
    const STATUS_CONFIRMED = 'Confirmado';
    const STATUS_FAILED = 'Fallido';
    const STATUS_REFUNDED = 'Revertido';
    const STATUS_CANCELED = 'Cancelado'; 

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'status',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];
    
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_FAILED => 'Fallido',
            self::STATUS_REFUNDED => 'Revertido',
            self::STATUS_CANCELED => 'Cancelado',
        ];
    }
    public static function getMethodOptions(): array
    {
        return [
            'contra_entrega' => 'Contra Entrega',
            'transferencia' => 'Transferencia',
        ];
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
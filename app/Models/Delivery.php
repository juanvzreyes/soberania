<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;
    
    const STATUS_PENDING_ASSIGNMENT = 'Inicial';
    const STATUS_IN_PREPARATION = 'En preparación';
    const STATUS_ON_THE_WAY = 'En camino';
    const STATUS_DELIVERED = 'Entregado';
    const STATUS_INCIDENT = 'Incidencia';
    const STATUS_CANCELED = 'Cancelado';

    protected $fillable = [
        'order_id',
        'transporter_id',
        'estimated_delivery_date',
        'status',
        'signature_path',
    ];
    
    protected $casts = [
        'estimated_delivery_date' => 'datetime',
        'status' => 'string',
    ];
    
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING_ASSIGNMENT => 'Inicial',
            self::STATUS_IN_PREPARATION => 'En preparación',
            self::STATUS_ON_THE_WAY => 'En camino',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_INCIDENT => 'Incidencia',
            self::STATUS_CANCELED => 'Cancelado',
        ];
    }
    
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }
}
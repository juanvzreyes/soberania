<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
class InventoryExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_item_id',
        'quantity',
        'reason',
        'user_id',
    ];
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    protected static function booted(): void
    {
        static::creating(function (InventoryExit $inventoryExit) {
            if (Auth::check()) {
                $inventoryExit->user_id = Auth::id();
            }
        });
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class); 
    }
}
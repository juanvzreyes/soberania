<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'order_item_id',
        'quantity',
        'reason',
    ];
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /*
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class); 
    }*/
}
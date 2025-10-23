<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class InventoryEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'reason',
        'user_id',
    ];
    protected static function booted(): void
    {
        static::creating(function (InventoryEntry $inventoryEntry) {
            if (Auth::check()) {
                $inventoryEntry->user_id = Auth::id();
            }
        });
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

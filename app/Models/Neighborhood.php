<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'postal_code',
        'municipality_id',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
}

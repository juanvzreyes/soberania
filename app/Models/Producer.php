<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producer extends Model
{
    use HasFactory;

    protected $fillable = [
        'certification',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->morphOne(Location::class, 'locationable');
    }

    public function phones()
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }
}

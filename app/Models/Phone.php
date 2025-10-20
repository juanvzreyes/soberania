<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'phones';

    protected $fillable = [
        'number',
        'dial_code',
        'type',
        'phoneable_id',
        'phoneable_type',
    ];

    public function phoneable()
    {
        return $this->morphTo();
    }
}

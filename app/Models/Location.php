<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'locations';

    protected $fillable = [
        'street',
        'postal_code',
        'exterior_number',
        'interior_number',
        'longitude',
        'latitude',
        'locationable_id',
        'locationable_type',
        'neighborhood_id',
        'municipality_id',
        'state_id',
    ];

    public function locationable()
    {
        return $this->morphTo();
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
}

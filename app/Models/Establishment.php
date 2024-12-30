<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Establishment extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_id',
        'name',
        'rate',
        'description'
    ];

    public function establishment_units() : HasMany
    {
        return $this->hasMany(EstablishmentUnit::class);
    }

    public function area() : BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}

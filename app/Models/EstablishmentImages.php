<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstablishmentImages extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_unit_id',
        'image'
    ];

    public function establishment_units() : BelongsTo
    {
        return $this->belongsTo(EstablishmentUnit::class);
    }

}

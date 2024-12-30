<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class EstablishmentUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'establishment_id',
        //0 acquired, 1 available, 2 on application, 3 maintenance, 4 archived
        'status'
    ];

    public function establishment() : BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function establishment_images() : HasMany
    {
        return $this->hasMany(EstablishmentImages::class);
    }

    public function business() : HasOne
    {
        return $this->hasOne(Business::class);
    }

}

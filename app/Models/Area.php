<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lat',
        'long',
        'image',
        'description',
    ];

    public function establishment() : HasMany
    {
        return $this->hasMany(Establishment::class);
    }

    public function business() : HasMany
    {
        return $this->hasMany(Business::class);
    }
}

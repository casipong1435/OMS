<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'psgcCode',
        'provDesc',
        'regCode',
        'provCode'
    ];

    
}

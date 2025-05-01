<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewPermitRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'permit_number',
        'expiration_date',
        'image',
        //0 under review, 1 approved, 2 rejected
        'status'
    ];

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

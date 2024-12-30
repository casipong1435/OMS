<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'establishment_unit_id',
        'kind_of_business',
        'area_id',
        'name',
        'plate',
        'permit_number',
        'dti_reg_number',
        //0 monthly, 1 quarterly, 2 bi-annual, 3 annual
        'payment_cycle',
        'cedula',
        //0 on checking, 1 approved, 2 reject, 3 closed
        'status',
        'permit_expiration_date',
        'remarks',
        'date_approved',
        'date_rejected',
        'date_closed'

    ];

    public function profile() : BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function requirement_image() : HasOne
    {
        return $this->hasOne(RequirementImage::class);
    }

    public function establishment_unit() : BelongsTo
    {
        return $this->belongsTo(EstablishmentUnit::class);
    }

    public function payment() : HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

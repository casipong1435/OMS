<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequirementImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'cedula',
        'brgy_clearance',
        'pmo_ceedo_clearance',
        'dti_cert',
        'medical_cert',
        'business_permit'
        /*'bof_protection',
        'sanitary_cert',
        'garbage_cert',
        'bldg_cert',
        'inspection_cert'*/
    ];

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'date_of_birth',
        'purok',
        'barangay',
        'city',
        'province',
        'region',
        'image'
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region() : BelongsTo
    {
        return $this->belongsTo(Region::class, 'region', 'regCode');
    }

    public function province() : BelongsTo
    {
        return $this->belongsTo(Province::class, 'province', 'provCode');
    }
    
    public function city() : BelongsTo
    {
        return $this->belongsTo(City::class, 'city', 'citymunCode');
    }
    public function barangay() : BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay', 'brgyCode');
    }

    public function ownBarangay() : BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay', 'brgyCode');
    }

    public function business() : HasOne
    {
        return $this->hasOne(Business::class);
    }
    

    
}

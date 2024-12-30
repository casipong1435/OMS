<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',  // Links to the business the payment belongs to
        'amount',       // The amount due or paid
        'due_date',     // The date the payment was due
        'paid_at',      // The date the payment was completed (nullable for unpaid payments)
        'remark'
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}

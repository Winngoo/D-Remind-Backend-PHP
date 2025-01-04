<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'coupon_code',
        'description',
        'discount',
        'discount_type',
        'applicable_plans',
        'expiration_date',
        'status',
    ];

    public function setApplicablePlansAttribute($value)
    {
        $this->attributes['applicable_plans'] = implode(',', $value);
    }

    public function getApplicablePlansAttribute($value)
    {
        return explode(',', $value);
    }

    protected $casts = [
        'applicable_plans' => 'array',
        'expiration_date' => 'date',
        'status' => 'boolean',
    ];
}

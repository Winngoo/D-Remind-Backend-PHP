<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'id','user_id', 'membership_id', 'payment_date', 'amount', 
        'plan_type', 'status', 'stripe_payment_id', 'end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership()
    {
        return $this->belongsTo(MembershipDetails::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipDetails extends Model
{
    use HasFactory;

    protected $fillable = ['id','membership_name', 'membership_benefits', 'membership_fee', 'vat', 'total_cost', 'validity'];

}

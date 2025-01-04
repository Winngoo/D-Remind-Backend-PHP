<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'category',
        'subcategory',
        'due_date',
        'time',
        'description',
        'provider',
        'cost',
        'payment_frequency',
        'reminder_status',
        'email_notification_status',
        'email10days',
        'email5days',
        'email3days',
        'email1day',
        'emailcurrentday',
        'sms_notification_status',
        'sms10days',
        'sms5days',
        'sms3days',
        'sms1day',
        'smscurrentday',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

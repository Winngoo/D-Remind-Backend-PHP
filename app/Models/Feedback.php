<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'email',
        'title',
        'description',
        'reply',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

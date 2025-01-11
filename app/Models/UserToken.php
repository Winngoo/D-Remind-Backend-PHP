<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    use HasFactory;

    protected $table = 'user_tokens';

    protected $fillable = ['id','user_id','token','device_name', 'is_primary'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

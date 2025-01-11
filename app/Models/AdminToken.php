<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminToken extends Model
{
    use HasFactory;

    protected $table = 'admin_tokens';

    protected $fillable = ['id','admin_id','token','device_name', 'is_primary'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}

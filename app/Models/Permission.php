<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = ['id', 'menu_name', 'permission_name'];


    
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_permissions_map', 'permission_id', 'user_id');
    }
}

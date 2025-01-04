<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function verifyAccount($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return redirect('/login')->with('error', 'User not found!');
        }

        $user->status = 'active';
        $user->save();

        return redirect('https://d-remind-winngoo.vercel.app/auth')->with('success', 'Account successfully activated!');
    }
}

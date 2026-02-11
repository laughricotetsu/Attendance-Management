<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {

            // adminだけ通す
            if (auth()->user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => '管理者ではありません']);
            }

            return redirect('/admin/attendance/list');
        }

        return back()->withErrors(['email' => 'ログイン失敗']);
    }
}

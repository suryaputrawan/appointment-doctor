<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showloginform()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $request['isaktif'] = 1;

        if (Auth::attempt($request->only('username', 'password'))) {

            Auth::logoutOtherDevices(request('password'));

            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('login')->with('error', 'Invalid username and password, or the account has been disabled..!');
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login');
    }
}

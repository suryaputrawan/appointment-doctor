<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        $request['isAktif'] = 1;

        if (Auth::attempt($request->only('username', 'password', 'isAktif'))) {

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

    public function showForgotPasswordForm()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        $user = User::where('email', $request->email)->where('isaktif', 1)->first();

        if ($user) {
            DB::beginTransaction();

            try {
                $password = Str::random(10);

                $user->update([
                    'password'              => Hash::make($password),
                    // 'password_change_at'    => null,
                ]);

                DB::commit();

                Mail::to($user->email)->send(new ResetPasswordMail($password));

                return redirect()->route('login')->with('success', 'Email has been send to [' . $request->email . ']. Please check for an email from ADOS to view your new password.');
            } catch (Throwable $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            }
        }

        return back()->with('error', 'Email address [' . $request->email . '] does not exist in the system !');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit()
    {
        try {
            $id = Auth::user()->id;
            $data = User::where('id', $id)->first();

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Not Found..");
            }

            return view('admin.modules.profile.edit', [
                'btnSubmit' => 'Update',
                'data'      =>  $data,
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'required|min:3|max:255',
            'email'     => 'required|email|unique:users,email,' . auth()->user()->id,
        ]);

        DB::beginTransaction();
        try {
            $id = Crypt::decryptString($id);
            $data = User::where('id', $id)->first();

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data Not Found");
            }

            $data->update([
                'name'      => $request->name,
                'email'     => $request->email
            ]);

            DB::commit();
            return redirect()
                ->back()
                ->with('success', 'Data profile has been updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->uncompromised(),
                'confirmed'
            ],
            'password_confirmation' => 'required',
        ]);

        if ($request->current_password) {
            if (!Hash::check($request->current_password, auth()->user()->password)) {
                throw ValidationException::withMessages([
                    'current_password'  =>  'current password not match, please try again',
                ]);
            }
        }

        try {
            auth()->user()->update([
                'password'  =>  Hash::make($request->password),
            ]);

            Auth::logout();
            Session::flush();

            return redirect()->route('login')->with('success', 'Password telah berhasil diperbaharui, Silahkan refresh browser dan login kembali..');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }
}

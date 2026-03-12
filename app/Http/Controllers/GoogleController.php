<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $findUser = User::where('google_id', $user->id)
                            ->orWhere('email', $user->email)
                            ->first();

            if ($findUser) {
                // Nếu chưa có google_id thì cập nhật (trường hợp user đã đăng ký bằng email trước đó)
                if (is_null($findUser->google_id)) {
                    $findUser->update(['google_id' => $user->id]);
                }
                
                // Tự động verify email nếu chưa
                if (is_null($findUser->email_verified_at)) {
                    $findUser->update(['email_verified_at' => now()]);
                }

                Auth::login($findUser);
                return redirect()->intended('/');
            } else {
                $userRole = Role::where('ten_vai_tro', 'user')->first();
                $newUser = User::create([
                    'ho_ten' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'role_id' => $userRole ? $userRole->id : 2,
                    'email_verified_at' => now(),
                    'password' => null, // Password nullable
                ]);

                Auth::login($newUser);
                return redirect()->intended('/');
            }
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Có lỗi xảy ra khi đăng nhập bằng Google.');
        }
    }
}

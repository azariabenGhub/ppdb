<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $guzzle = new Client(['verify' => false]);

        return Socialite::driver('google')
            ->setHttpClient($guzzle)
            ->redirectUrl(config('services.google.redirect'))
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $guzzle = new Client(['verify' => false]);

            $googleUser = Socialite::driver('google')
                ->setHttpClient($guzzle)
                ->redirectUrl(config('services.google.redirect'))
                ->user();

            Log::info('Google User Data:', (array) $googleUser);

            $email = $googleUser->getEmail();
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                if (is_null($existingUser->google_id)) {
                    // Akun sudah ada tapi bukan dari Google
                    return redirect('/login')->withErrors([
                        'email' => 'Akun ini terdaftar menggunakan password. Silakan login menggunakan email dan password.'
                    ]);
                }
                $user = $existingUser;
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'pendaftar',
                    'google_id' => $googleUser->getId(),
                ]);
                $user->email_verified_at = now();
                $user->save();
                Log::info('New user created via Google:', ['id' => $user->id, 'email' => $email]);
            }

            // Buat token Sanctum
            $token = $user->createToken('google-auth-token')->plainTextToken;

            return redirect()->route('google.auth.success', [
                'token' => $token,
                'user' => base64_encode(json_encode([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]))
            ]);

        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['google' => 'Gagal login dengan Google: ' . $e->getMessage()]);
        }
    }

    public function success(Request $request)
    {
        $token = $request->query('token');
        $userEncoded = $request->query('user');

        if (!$token || !$userEncoded) {
            return redirect('/login')->withErrors(['google' => 'Data tidak lengkap.']);
        }

        $user = json_decode(base64_decode($userEncoded), true);

        return view('auth.google-auth-callback', [
            'token' => $token,
            'user' => $user,
        ]);
    }
}
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Register a new user and issue a Sanctum token.
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        
        // Hapus token reCAPTCHA dan password_confirmation
        unset($validated['g-recaptcha-response']);
        unset($validated['password_confirmation']);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'pendaftar',
        ]);

        // Kirim email verifikasi
        event(new Registered($user));

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan cek email Anda untuk memverifikasi akun sebelum masuk.',
            'user' => $user,
        ], 201);
    }

    /**
     * Authenticate user and issue a Sanctum token.
     */
    public function login(LoginRequest $request)
    {
        // Ambil hanya email dan password, jangan sertakan token reCAPTCHA
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        if ($user->google_id !== null) {
            return response()->json(['message' => 'Akun ini terdaftar dengan Google. Silakan login menggunakan Google.'], 401);
        }

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email Anda belum terverifikasi. Silakan periksa kotak masuk email Anda.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * Revoke the token that was used to authenticate the current request.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
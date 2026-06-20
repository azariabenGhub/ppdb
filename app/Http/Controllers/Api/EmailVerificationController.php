<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return redirect('/login?error=invalid');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/login?verified=already');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect('/login?verified=success');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);
            $user = User::where('email', $request->email)->first();
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email Anda sudah terverifikasi.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link verifikasi baru telah dikirim ke email Anda.']);
    }
}

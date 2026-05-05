<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['panitia', 'bendahara'])->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:panitia,bendahara',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:panitia,bendahara',
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return response()->json($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        // Pastikan hanya role panitia atau bendahara yang bisa diakses
        if (!in_array($user->role, ['panitia', 'bendahara'])) {
            return response()->json(['message' => 'User tidak valid'], 404);
        }
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Jangan hapus diri sendiri (kepala sekolah yang login)
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri.'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'Staff berhasil dihapus.']);
    }
}

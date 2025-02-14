<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'no_induk' => 'required|unique:users,no_induk',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'no_induk' => $validated['no_induk'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false, 
        ]);


        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data' => [
                'user' => $user,
            ],
        ], 201);
    }


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'no_induk' => 'required',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User berhasil login',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil logout',
        ]);
    }
}

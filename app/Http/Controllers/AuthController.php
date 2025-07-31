<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->validated();

        $request->authenticate();

        $user = Auth::user();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->data([
                    'token' => $token,
                    'user' => $user,
        ], 'Login successful.');
    }

    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:male,female',
            'birthday' => 'required|date|before:today',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->data([
                    'token' => $token,
                    'user' => $user,
        ], 'Registed successful.');
    }
}

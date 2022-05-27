<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string',
            'email'         => 'required|string|unique:users,email',
            'password'      => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->symbols()->numbers(),
            ],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken(env('API_TOKEN'))->plainTextToken;

        return response([
            "user"  => $user,
            "token" => $token,
        ], 201);
    }
}

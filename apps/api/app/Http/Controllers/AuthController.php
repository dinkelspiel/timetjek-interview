<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email_or_pin' => ['required'],
            'mode' => ['required'],
            'password' => ['required'],
        ]);


        if (
            Auth::attempt([
                $credentials['mode'] => $credentials['email_or_pin'],
                'password' => $credentials['password']
            ])
        ) {
            $request->session()->regenerate();

            $token = $request->user()->createToken("login");

            return response()->json([
                "message" => "Login successfull",
                "token" => $token->plainTextToken,
            ]);
        }

        return response()->json([
            "message" => "The provided credentials are incorrect.",
        ], 401);
    }

    public function signup(Request $request)
    {
        $validated = $request->validate([
            'firstname' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'pin' => ['required', 'string', 'size:10'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'pin' => $validated['pin'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $token = $user->createToken("signup");

        $request->session()->regenerate();

        return response()->json([
            "message" => "Account created",
            "token" => $token->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();

        return response()->json([
            "message" => "Successfully logged out",
        ], 200);
    }

    public function validate(Request $request)
    {
        return response()->json([
            "message" => "Session is valid",
            "user" => Auth::user(),
        ]);
    }
}

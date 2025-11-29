<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'min:8'],
            'confirm_password' => ['required', 'same:new_password'],
        ]);

        if (!\Hash::check($request->old_password, $user->password)) {
            return response()->json([
                "message" => "Old password is incorrect.",
            ], 400);
        }

        $user->password = $request->new_password;
        $user->save();

        return response()->json([
            "message" => "Password changed.",
        ]);
    }
}

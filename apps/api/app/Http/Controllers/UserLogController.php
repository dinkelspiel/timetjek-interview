<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLogController extends Controller
{
    public function show()
    {
        return response()->json([
            "message" => "Found logs",
            "data" => UserLog::where("user_id", Auth::user()->id)->orderByDesc("id")->get()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\UserAttendance;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'latitude' => 'numeric|between:-90,90',
            'longitude' => 'numeric|between:-180,180',
        ]);

        $user = $request->user();

        $activeAttendance = UserAttendance::where("user_id", $user->id)->whereNull("check_out_at")->first();
        if ($activeAttendance != null) {
            if (UserAttendance::doesOverlap($activeAttendance->check_in_at, Carbon::now(), $activeAttendance->id)) {
                return response()->json([
                    "message" => "You can't check out now since there is an active attendance already in place"
                ], 400);
            }

            $activeAttendance->update([
                "check_out_at" => now()
            ]);

            return response()->json([
                "message" => "Checked out"
            ]);
        }

        if (UserAttendance::doesOverlap(Carbon::now(), Carbon::now(), null)) {
            return response()->json([
                "message" => "You can't check in now since there is an active attendance already in place"
            ], 400);
        }

        UserAttendance::create([
            "user_id" => $user->id,
            "check_in_at" => now(),
            "check_out_at" => null,
            "latitude" => $request->json("latitude"),
            "longitude" => $request->json("longitude")
        ]);

        return response()->json([
            "message" => "Checked in",
        ]);
    }

    public function getActive(Request $request)
    {
        $user = $request->user();

        $activeAttendance = UserAttendance::where("user_id", $user->id)->whereNull("check_out_at")->first();

        return response()->json([
            "message" => $activeAttendance != null ? "Found active" : "No active found",
            "data" => $activeAttendance
        ]);
    }

    public function show(Request $request)
    {
        $user = $request->user();

        $attendances = UserAttendance::where("user_id", $user->id)
            ->where("check_in_at", ">", now()->subDays(7))
            ->orWhere('check_out_at', '>', now()->subDays(7))
            ->get();

        return response()->json([
            "message" => "Found attendances",
            "data" => $attendances
        ]);
    }

    public function update(Request $request, UserAttendance $attendance)
    {
        $validated = $request->validate([
            'check_in_at' => ['required', 'date'],
            'check_out_at' => ['date', 'after:check_in_at'],
            'reason' => ['required']
        ]);


        // Check for overlaps
        if (UserAttendance::doesOverlap(Carbon::parse($validated['check_in_at']), Carbon::parse($validated['check_out_at']), $attendance->id)) {
            return response()->json([
                'message' => "The updated attendance can't overlap an existing attendance"
            ], 400);
        }

        // Add update to user logs
        $newCheckIn = Carbon::parse($validated['check_in_at'])->format('Y-m-d H:i:s');
        $newCheckOut = Carbon::parse($validated['check_out_at'])->format('Y-m-d H:i:s');

        $oldCheckoutMessage = "";
        if ($attendance->check_out_at != null) {
            $oldCheckoutMessage = " and " . $attendance->check_out_at->format('Y-m-d H:i:s');
        }

        UserLog::create([
            "user_id" => $request->user()->id,
            "message" => "Updated check in and check out time from {$attendance->check_in_at->format('Y-m-d H:i:s')}{$oldCheckoutMessage} to {$newCheckIn} and {$newCheckOut} because of {$request->json("reason")}"
        ]);

        $attendance->update([
            'check_in_at' => $validated["check_in_at"],
            'check_out_at' => $validated["check_out_at"],
        ]);

        return response()->json([
            'message' => "Updated attendance"
        ]);
    }
}

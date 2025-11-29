<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserAttendance extends Model
{
    protected $fillable = [
        'user_id',
        'check_in_at',
        'check_out_at',
        'latitude',
        'longitude',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public static function doesOverlap(Carbon $from, Carbon $to, int|null $excludeId)
    {
        $countA = UserAttendance::where('id', '!=', $excludeId)->where('user_id', Auth::user()->id)->where('check_in_at', '<', $from)->where('check_out_at', '>', $from)->count();
        $countB = UserAttendance::where('id', '!=', $excludeId)->where('user_id', Auth::user()->id)->where('check_in_at', '<', $to)->where('check_out_at', '>', $to)->count();

        return $countA + $countB > 0;
    }
}

<?php

use App\Http\Controllers\AccountTransactionsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserAccountsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserInstitutionKeysController;
use App\Http\Controllers\UserInstitutionRemoteController;
use App\Http\Controllers\UserLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/ping", fn() => response("pong"));

Route::prefix("v1")->group(function () {
    Route::prefix("auth")->group(function () {
        Route::get("validate", [AuthController::class, "validate"])->middleware("auth:sanctum");
        Route::post("login", [AuthController::class, "login"]);
        Route::post("signup", [AuthController::class, "signup"]);
        Route::get("logout", [AuthController::class, "logout"]);
    });

    Route::prefix("user")->middleware("auth:sanctum")->group(function () {
        Route::post("password", [UserController::class, "updatePassword"]);
        Route::get("logs", [UserLogController::class, "show"]);
    });

    Route::prefix("attendances")->middleware("auth:sanctum")->group(function () {
        Route::get("", [AttendanceController::class, "show"]);
        Route::post("check", [AttendanceController::class, "check"]);
        Route::get("active", [AttendanceController::class, "getActive"]);
        Route::post("{attendance}", [AttendanceController::class, "update"]);
    });
});

<?php

use App\Http\Controllers\MailController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;

Route::post('/send-mail', [MailController::class , 'sendMail']);

Route::post('/register', [AuthController::class , 'register']);
Route::post('/login', [AuthController::class , 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
            return $request->user();
        }
        );
        Route::post('/logout', [AuthController::class , 'logout']);    });

Route::apiResource('Products', ProductController::class);
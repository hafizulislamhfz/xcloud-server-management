<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticationController;

Route::post('login', [AuthenticationController::class, 'store']);

Route::middleware('auth:sanctum')
    ->post('logout', [AuthenticationController::class, 'destroy']);

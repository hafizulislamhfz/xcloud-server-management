<?php

use App\Http\Controllers\Api\V1\Server\ServerController;

Route::group([
    'prefix' => 'servers',
    'middleware' => ['auth:sanctum', 'throttle:60,1'],
], function () {
    Route::get('/', [ServerController::class, 'index']);

    Route::post('/', [ServerController::class, 'store']);

    Route::post('/bulk-action', [ServerController::class, 'bulkAction']);

    Route::get('{id}', [ServerController::class, 'show'])
        ->where('id', '[0-9]+');

    Route::put('{id}', [ServerController::class, 'update'])
        ->where('id', '[0-9]+');

    Route::delete('{id}', [ServerController::class, 'destroy'])
        ->where('id', '[0-9]+');
});

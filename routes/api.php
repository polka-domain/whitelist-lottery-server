<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('hello', function () {
    return 'Hello Polka Domain!';
});

Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'store']);
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{address}', [UserController::class, 'show']);
});

Route::get('/whitelist/status', function () {
    $now = now()->timestamp;
    if (env('WHITELIST_TIMESTAMP_BEGIN') <= $now && $now < env('WHITELIST_TIMESTAMP_END')) {
        return response('');
    }
    return response('', 400);
});

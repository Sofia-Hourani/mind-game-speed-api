<?php

use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('game/{id}/submit', [GameController::class, 'submitGame'])
    ->name('game.submit');

Route::post('game/start', [GameController::class, 'startGame'])->name('game.start');

Route::get('game/{id}/end', [GameController::class, 'endGame'])
    ->name('game.end');

<?php

use App\Http\Controllers\Api\CoachController;
use App\Http\Controllers\Api\PlayerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClubController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/club'], function () {
    Route::post('/new', [ClubController::class, 'store'])->name('club.new-club');
    Route::post('/new-coach', [ClubController::class, 'addCoach'])->name('club.new-coach');
    Route::post('/new-player', [ClubController::class, 'addPlayer'])->name('club.new-player');
    Route::post('/player/remove', [ClubController::class, 'removePlayer'])->name('club.remove-player');
    Route::post('/coach/remove', [ClubController::class, 'removeCoach'])->name('club.remove-coach');
    Route::post('/change-budget', [ClubController::class, 'changeBudget'])->name('club.change-budget');
    Route::get('/list-players', [ClubController::class, 'listPlayers'])->name('club.list-players');
});

Route::group(['prefix' => '/coach'], function () {
    Route::post('/new', [CoachController::class, 'store'])->name('new-coach');
});

Route::group(['prefix' => '/player'], function () {
    Route::post('/new', [PlayerController::class, 'store'])->name('new-player');
});

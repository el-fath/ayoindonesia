<?php

use Illuminate\Http\Request;
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
Route::post('login', 'Auth\AuthController@login')->name('login');
Route::resource('users', 'UserController');

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('logout', 'Auth\AuthController@logout');
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    
    Route::resource('matches', 'MatchesController')->only(['index', 'store']);
    Route::get('matches/{matches}', 'MatchesController@show')->name('show');
    Route::put('matches/{matches}', 'MatchesController@update')->name('update');
    Route::delete('matches/{matches}', 'MatchesController@destroy')->name('destroy');
    
    Route::resource('players', 'PlayerController');

    Route::resource('teams', 'TeamController')->except('update');
    Route::post('teams/{team}', 'TeamController@update')->name('update');

});
<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(HomeController::class)->group(function(){

    Route::middleware(['throttle:global'])->group(function(){
        Route::get('/suche', 'search')->name('search');
        Route::get('/stadt/{stadt}', 'city')->name('city');
    });

    Route::get('/', 'home')->name('home');
    Route::get('/home', 'home')->name('home');
    Route::get('/datenschutz', 'privacyPolicy')->name('privacyPolicy');
    Route::get('/impressum', 'imprint')->name('imprint');
    Route::get('/liste', 'list')->name('list');
    Route::get('/liste/{buchstabe}', 'listByLetter')->name('listByLetter');
});


Auth::routes();


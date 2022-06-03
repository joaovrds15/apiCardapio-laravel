<?php

use App\Http\Controllers\ApiItemsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ApiAuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix'     => 'auth'
], function($router){
    Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
    Route::post('/register', [ApiAuthController::class, 'register'])->name('register.api');
    Route::group([
        'middleware' => 'JWTVerification',
    ], function($router){
        Route::get('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');
    });
    

});

Route::group([
    'middleware' => 'JWTVerification',
    'prefix'    =>  'items'
], function($router){
    Route::post('/create',[ApiItemsController::class, 'create'])->name('create.api');
    Route::get('/list/{id}',[ApiItemsController::class,'listId'])->name('list.api');
    Route::get('/list',[ApiItemsController::class,'list'])->name('list.api');
    Route::get('/search/{name}',[ApiItemsController::class,'search'])->name('search.api');
    Route::get('edit/{id}',[ApiItemsController::class,'listId'])->name('editGet.api');
    Route::put('edit/{id}',[ApiItemsController::class,'edit'])->name('editGet.api');
    Route::delete('delete/{id}',[ApiItemsController::class,'delete'])->name('delete.api');
    
});



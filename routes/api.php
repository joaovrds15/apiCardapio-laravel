<?php

declare(strict_types=1);

use App\Http\Controllers\ApiItemsController;
use App\Http\Controllers\Auth\ApiAuthController;
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

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::group([
        'prefix' => 'auth',
    ], function ($router) {
        Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
        Route::post('/register', [ApiAuthController::class, 'register'])->name('register.api');
        Route::group([
            'middleware' => 'auth:api',
        ], function ($router) {
            Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout.api');
        });
    });

    Route::group([
        'middleware' => 'auth:api',
        'prefix' => 'items',
    ], function ($router) {
        Route::post('/create', [ApiItemsController::class, 'create'])->name('create.api');
        Route::get('/list/{id}', [ApiItemsController::class, 'listId'])->name('list.api');
        Route::get('/list', [ApiItemsController::class, 'list'])->name('list.api');
        Route::get('/search/{name}', [ApiItemsController::class, 'search'])->name('search.api');
        Route::get('edit/{id}', [ApiItemsController::class, 'listId'])->name('editGet.api');
        Route::put('edit/{id}', [ApiItemsController::class, 'edit'])->name('editGet.api');
        Route::delete('delete/{id}', [ApiItemsController::class, 'delete'])->name('delete.api');
    });
});

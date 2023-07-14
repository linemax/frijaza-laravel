<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SendMailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('subscribe', [SendMailController::class, 'sendEmail']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get(
        '/user',
        function (Request $request) {
            return $request->user();
        }
    );


    Route::post(
        '/user/logout',
        function (Request $request) {
            Auth::guard('web')->logout();
            return response(status: 200);
        }
    );

    Route::prefix('users')->name('users')->controller(UserController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::get('search', 'search');
        Route::get('{user}', 'show');
        Route::post('{user}', 'update');
        Route::delete('{user}', 'destroy');
    });


    Route::prefix('posts')->controller(PostController::class)->group(
        function () {
            Route::get('', 'index')->withoutMiddleware('auth:sanctum');
            Route::post('', 'store');
            Route::post('{post}', 'update');
            Route::get('{post}', 'show')->withoutMiddleware('auth:sanctum');
            Route::delete('{post}', 'destroy');
            Route::post('{post}/photo', 'photo');
            Route::get('{post}/photo', 'getPhoto')->withoutMiddleware('auth:sanctum');
        }
    );



    Route::prefix('authors')->name('authors')->controller(AuthorController::class)->group(
        function () {
            Route::get('', 'index')->withoutMiddleware('auth:sanctum');
            Route::post('', 'store');
            Route::post('{author}', 'update');
            Route::get('search', 'search');
            Route::post('{author}/photo', 'photo');
            Route::get('{author}/photo', 'getPhoto')->withoutMiddleware('auth:sanctum');
            Route::get('{author}', 'show')->name('.show')->withoutMiddleware('auth:sanctum');
            Route::delete('{author}', 'destroy');
        }
    );


    Route::prefix('categories')->controller(CategoryController::class)->group(
        function () {
            Route::get('', 'index')->withoutMiddleware('auth:sanctum');
            Route::post('', 'store');
            Route::post('{category}', 'update');
            Route::get('{category}', 'show')->withoutMiddleware('auth:sanctum');
            Route::delete('{category}', 'destroy');
        }
    );


    Route::prefix('services')->controller(ServiceController::class)->group(
        function () {
            Route::get('', 'index')->withoutMiddleware('auth:sanctum');
            Route::post('', 'store');
            Route::post('{service}', 'update');
            Route::get('{service}', 'show')->withoutMiddleware('auth:sanctum');
            Route::delete('{service}', 'destroy');
            Route::post('{service}/photo', 'photo');
            Route::get('{service}/photo', 'getPhoto')->withoutMiddleware('auth:sanctum');
        }
    );


});
Route::prefix('photos')->controller(PhotoController::class)->group(
    function () {
        Route::get('', 'index')->withoutMiddleware('auth:sanctum');
        Route::post('', 'store');
        Route::post('{photo}', 'update');
        Route::get('{photo}', 'show')->withoutMiddleware('auth:sanctum');
        Route::get('{photo}/serve', 'serve')->withoutMiddleware('auth:sanctum');
        Route::post('delete/selected', 'deleteMultiple');
        Route::delete('{photo}', 'destroy');
    }
);
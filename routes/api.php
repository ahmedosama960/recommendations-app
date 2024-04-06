<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReadingIntervalController;
use App\Http\Controllers\UserController;
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
    Route::resource('users', BookController::class);

    Route::resource('books', BookController::class);

    Route::put('books/restore/{id}',[BookController::class,'restore']);
    // top 5 books
    Route::get('books/recommendations/top-five', [BookController::class, 'topFiveBooks']);

    Route::get('books/recommendations/top-five/v2', [BookController::class, 'topFiveBooksV2']);

    Route::resource('intervals', ReadingIntervalController::class);

<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/customers', CustomerController::class);
Route::apiResource('/books', BookController::class);
Route::patch('/books/{bookId}/rent', [BookController::class, 'rent']);
Route::patch('/books/{bookId}/return', [BookController::class, 'return']);

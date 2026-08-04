<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
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
Route::get('/', [BookController::class, 'index']);
Route::get('/books', [BookController::class, 'index'])->name('books.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');
});

Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

//あとで実装するランキング機能
Route::get('/ranking', fn() => 'ランキング画面')->name('ranking.index');

//あとで実装するお気に入り一覧
Route::get('/favorites', fn() => 'お気に入り一覧画面')->name('favorites.index');

//あとで実装するジャンル一覧
Route::get('/genres', fn() => 'ジャンル一覧画面')->name('genres.index');
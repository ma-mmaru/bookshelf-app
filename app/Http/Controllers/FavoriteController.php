<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Book $book)
    {
        auth()->user()->favoriteBooks()->toggle($book->id);

        return back();
    }
}
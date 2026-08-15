<?php

namespace App\Http\Controllers;

use App\Models\Book;

class RankingController extends Controller
{
    public function __invoke()
    {
        $rankedBooks = Book::has('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->with('genres')
            ->orderByDesc('reviews_avg_rating')
            ->limit(10)
            ->get();

        return view('ranking.index', compact('rankedBooks'));
    }
}
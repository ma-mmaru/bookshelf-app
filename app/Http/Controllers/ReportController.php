<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalReviews = $user->reviews()->count();
        $booksRead = $user->reviews()->distinct('book_id')->count('book_id');
        $averageRating = $user->reviews()->avg('rating') ?? 0;

        $ratingCounts = $user->reviews()
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $ratingDistribution = collect([
            0 => $ratingCounts[1] ?? 0,
            1 => $ratingCounts[2] ?? 0,
            2 => $ratingCounts[3] ?? 0,
            3 => $ratingCounts[4] ?? 0,
            4 => $ratingCounts[5] ?? 0,
        ]);

        $topRatedBooks = $user->reviews()
            ->with('book')
            ->where('rating', '>=', 4)
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->filter(fn($review) => $review->book !== null)
            ->map(function ($review) {
                return [
                    'id' => $review->book->id,
                    'title' => $review->book->title,
                    'author' => $review->book->author,
                    'rating' => $review->rating,
                ];
            })
            ->values()
            ->toArray();

        $genreRatings = DB::table('book_genre')
        ->join('books', 'book_genre.book_id', '=', 'books.id')
        ->join('reviews', 'books.id', '=', 'reviews.book_id')
        ->join('genres', 'book_genre.genre_id', '=', 'genres.id')
        ->where('reviews.user_id', $user->id)
        ->select('genres.id', 'genres.name', DB::raw('avg(reviews.rating) as average_rating'), DB::raw('count(reviews.id) as count'))
        ->groupBy('genres.id', 'genres.name')
        ->orderBy('average_rating', 'desc')
        ->take(5)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'count' => $item->count,
                'average_rating' => (float) $item->average_rating,
            ];
        })->toArray();

        $stats = [
            'summary' => [
                'total_reviews' => $totalReviews,
                'books_read' => $booksRead,
                'average_rating' => $averageRating,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}
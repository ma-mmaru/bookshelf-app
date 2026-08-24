<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookIndexRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\Api\V1\BookDetailResource;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(BookIndexRequest $request)
    {
        $query = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if ($genreId = $request->input('genre_id')) {
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        $perPage = $request->input('per_page', 10);
        $books = $query->latest()->paginate($perPage);

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load(['genres', 'reviews.user']);

        return new BookDetailResource($book);
    }

    public function store(StoreBookRequest $request)
    {
        $book = DB::transaction(function () use ($request) {
            $data = $request->only([
                'title', 'author', 'isbn', 'description', 'published_date'
            ]);

            $data['user_id'] = $request->user()->id;

            $book = Book::create($data);

            if ($request->has('genre_ids')) {
                $book->genres()->sync($request->input('genre_ids'));
            }

            return $book;
        });

        $book->load(['genres', 'reviews.user']);

        return (new BookDetailResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        if ($request->user()->id !== $book->user_id) {
            return response()->json([
                'message' => 'この書籍を修正する権限がありません。'
            ], 403);
        }

        DB::transaction(function () use ($request, $book) {
            $book->update($request->only([
                'title', 'author', 'isbn', 'description', 'published_date'
            ]));

            if ($request->has('genre_ids')) {
                $book->genres()->sync($request->input('genre_ids'));
            }
        });

        $book->load(['genres', 'reviews.user']);

        return new BookDetailResource($book);
    }

    public function destroy(Request $request, Book $book)
    {
        if ($request->user()->id !== $book->user_id) {
            return response()->json([
                'message' => 'この書籍を削除する権限がありません。'
            ], 403);
        }

        DB::transaction(function () use ($book) {
            $book->genres()->detach();

            if (method_exists($book, 'reviews')) {
                $book->reviews()->delete();
            }

            if (method_exists($book, 'favorites')) {
                $book->favorites()->delete();
            }

            $book->delete();
        });

        return response()->json([
            'message' => '書籍を削除しました。'
        ], 200);
    }
}
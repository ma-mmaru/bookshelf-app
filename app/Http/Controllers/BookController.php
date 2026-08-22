<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Http\Requests\BookRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()->with('genres')->withAvg('reviews', 'rating');

        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if ($genreId = $request->input('genre')) {
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'rating':
                $query->orderByRaw('reviews_avg_rating IS NULL ASC')
                        ->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $books =$query->paginate(10)->withQueryString();

        $genres = Genre::all();

        return view('books.index', compact('books', 'genres'));
    }

    public function show(Book $book)
    {
        $book->load('genres');

        return view('books.show', compact('book'));
    }

    public function create()
    {
        $genres = Genre::all();
        return view('books.create', compact('genres'));
    }

    public function store(BookRequest $request)
    {
        $validated = $request->validated();

        $book = auth()->user()->books()->create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        $book->genres()->sync($validated['genres']);

        return redirect()->route('books.show', $book)->with('status', '書籍を登録しました。');
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $book->load('genres');
        $genres = Genre::all();
        return view('books.edit', compact('book', 'genres'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validated();

        $book->update([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'published_date' => $validated['published_date'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        $book->genres()->sync($validated['genres']);

        return redirect()->route('books.show', $book)->with('status', '書籍情報を更新しました。');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')->with('status', '書籍を削除しました。');
    }
}
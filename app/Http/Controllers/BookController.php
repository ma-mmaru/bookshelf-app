<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('genres')
        ->latest()
        ->paginate(10);

        return view('books.index', compact('books'));
    }

    public function show(Book $book)
    {
        $book->load('genres');

        return view('books.show', compact('book'));
    }

    public function create()
    {
        //あとで実装エラー回避
        $genres = collect();
        return view('books.create', compact('genres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max255'],
        ]);

        Book::create($validated);

        return redirect()->route('books.index')->with('success', '書籍を登録しました。');
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $book->load('genres');
        return view('books.edit', compact('book'));
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')->with('status', '書籍を削除しました。');
    }
}
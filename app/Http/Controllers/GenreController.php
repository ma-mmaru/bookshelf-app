<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Http\Requests\GenreRequest;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    public function destroy(Genre $genre)
    {
        if ($genre->books()->exists()) {
            return back()->withErrors(['error' => '書籍が紐づいているジャンルは削除できません。']);
        }

        $genre->delete();

        return redirect()->route('genres.index')
            ->with('status', 'ジャンルを削除しました。');
    }

    public function show(Genre $genre)
    {
        $books = $genre->books()
            ->with('genres')
            ->latest()
            ->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    public function create()
    {
        return view ('genres.create');
    }

    public function store(GenreRequest $request)
    {
        Genre::create($request->validated());

        return redirect()->route('genres.index')->with('status', 'ジャンルを登録しました。');
    }

    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return redirect()->route('genres.index')->with('status', 'ジャンル名を更新しました。');
    }
}
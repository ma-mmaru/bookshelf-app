<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
Use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_ジャンルのリレーションが定義されている(): void
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'User@example.com',
            'password' => Hash::make('password'),
        ]);

        $genre = Genre::create(['name' => '小説']);

        $book = Book::create([
            'user_id' => $user->id,
            'title' => '小説本',
            'author' => '作家',
            'isbn' => '9784123456789',
            'published_date' => '2026-01-01',
        ]);

        $genre->books()->attach($book);

        $this->assertTrue($genre->books->contains($book));
    }
}
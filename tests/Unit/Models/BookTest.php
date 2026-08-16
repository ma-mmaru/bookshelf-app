<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍のリレーションが定義されている(): void
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $genre = Genre::create(['name' => 'プログラミング']);

        $book = Book::create([
            'user_id' => $user->id,
            'title' => 'テスト本',
            'author' => '著者',
            'isbn' => '9784123456789',
            'published_date' => '2026-01-01',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'いい本です',
        ]);

        $book->genres()->attach($genre);
        $book->favoritedByUsers()->attach($user);

        $this->assertTrue($book->user->is($user));
        $this->assertTrue($book->genres->contains($genre));
        $this->assertTrue($book->reviews->contains($review));
        $this->assertTrue($book->favoritedByUsers->contains($user));
    }
}
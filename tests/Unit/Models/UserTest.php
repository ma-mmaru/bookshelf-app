<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_ユーザーのリレーションが定義されている(): void
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

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
            'comment' => '最高でした',
        ]);

        $user->favoriteBooks()->attach($book);
        $user->likedReviews()->attach($review);

        $this->assertTrue($user->books->contains($book));
        $this->assertTrue($user->reviews->contains($review));
        $this->assertTrue($user->favoriteBooks->contains($book));
        $this->assertTrue($user->likedReviews->contains($review));
    }
}
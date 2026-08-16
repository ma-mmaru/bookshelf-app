<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
Use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_レビューのリレーションが定義されている(): void
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
            'rating' => 4,
            'comment' => 'おすすめ',
        ]);

        $review->likedByUsers()->attach($user);

        $this->assertTrue($review->user->is($user));
        $this->assertTrue($review->book->is($book));
        $this->assertTrue($review->likedByUsers->contains($user));
    }
}
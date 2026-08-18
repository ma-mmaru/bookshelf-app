<?php

namespace Tests\Feature;

Use App\Models\Book;
Use App\Models\Review;
Use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍にレビューを投稿できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'rating' => 5,
            'comment' => '素晴らしい本でした。',
        ];

        $response = $this->actingAs($user)->post(route('reviews.store', $book), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => '素晴らしい本でした。',
        ]);
    }

    public function test_レビュー投稿のバリデーションエラー(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'rating' => 10,
            'comment' => '',
        ];

        $response = $this->actingAs($user)->post(route('reviews.store', $book), $data);

        $response->assertSessionHasErrors(['rating', 'comment']);
    }

    public function test_レビューにいいねをトグル送信できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.like', $review));
        $response->assertRedirect();
        $this->assertDatabaseHas('review_user', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }
}
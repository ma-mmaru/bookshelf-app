<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewAndFavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_レビューの削除ができること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));
        $response->assertStatus(302);
    }

    public function test_お気に入りのトグル解除ができること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book->id);

        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));
        $response->assertStatus(302);

        $this->assertDatabaseMissing('book_user', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_ジャンルの削除ができること()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));
        $response->assertStatus(302);
    }
}
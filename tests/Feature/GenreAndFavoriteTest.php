<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreAndFavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_ジャンル一覧表示および作成ができる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $this->actingAs($user)->get(route('genres.index'))->assertStatus(200);

        $response = $this->actingAs($user)->post(route('genres.store'), ['name' => '新ジャンル']);
        $response->assertRedirect();
        $this->assertDatabaseHas('genres', ['name' => '新ジャンル']);
    }

    public function test_お気に入りの登録および解除ができる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAs($user)->post(route('favorites.toggle', $book))->assertRedirect();
        $this->assertDatabaseHas('book_user', ['user_id' => $user->id, 'book_id' => $book->id]);

        $this->actingAs($user)->post(route('favorites.toggle', $book))->assertRedirect();
        $this->assertDatabaseMissing('book_user', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_ランキング画面を表示できる(): void
    {
        $this->get(route('ranking.index'))->assertStatus(200);
    }
}
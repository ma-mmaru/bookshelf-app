<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_書籍一覧および詳細画面が表示される(): void
    {
        $book = Book::factory()->create();

        $this->get(route('books.index'))->assertStatus(200);
        $this->get(route('books.show', $book))->assertStatus(200);
    }

    public function test_書籍を新規登録できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '9784798160000',
            'published_date' => '2024-01-01',
            'description' => 'テスト概要',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->post(route('books.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', ['title' => 'テスト書籍', 'isbn' => '9784798160000']);
    }

    public function test_書籍登録時のバリデーションエラー(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), []);

        $response->assertSessionHasErrors(['title', 'author', 'isbn', 'published_date', 'genres']);
    }

    public function test_重複したISBNで書籍登録できない(): void
    {
        $user = User::factory()->create();
        $existingBook = Book::factory()->create(['isbn' => '9784798160000']);
        $genre = Genre::factory()->create();

        $data = [
            'title' => '重複テスト',
            'author' => '著者',
            'isbn' => '9784798160000',
            'published_date' => '2024-01-01',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->post(route('books.store'), $data);

        $response->assertSessionHasErrors(['isbn']);
    }

    public function test_書籍情報を更新できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'title' => '更新後のタイトル',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => $book->published_date,
            'description' => '更新後の概要',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)->put(route('books.update', $book), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => '更新後のタイトル']);
    }

    public function test_書籍を削除できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect();
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
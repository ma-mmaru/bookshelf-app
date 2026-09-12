<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_他人の作成した書籍データの更新および削除が拒否されること(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)->put(route('books.update', $book), [
            'title' => '不正更新',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => $book->published_date,
        ])->assertStatus(403);

        $this->actingAs($otherUser)->delete(route('books.destroy', $book))->assertStatus(403);

    }
}

<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchAndSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_タイトルや著者名による検索・フィルタリングができること(): void
    {
        $user = User::factory()->create();
        $targetBook = Book::factory()->create(['title' => 'Laravel入門', 'author' => '山田太郎']);
        $otherBook = Book::factory()->create(['title' => 'PHP基礎', 'author' => '鈴木花子']);

        $response = $this->actingAs($user)->get(route('books.index', ['keyword' => 'Laravel']));

        $response->assertOk();
        $response->assertSee($targetBook->title);
        $response->assertDontSee($otherBook->title);
    }

    public function test_指定したカラムや昇順降順でソートができること(): void
    {
        $user = User::factory()->create();
        $bookA = Book::factory()->create(['title' => 'AAA', 'created_at' => now()->subDays(2)]);
        $bookB = Book::factory()->create(['title' => 'BBB', 'created_at' => now()]);

        $response = $this->actingAs($user)->get(route('books.index', ['sort' => 'created_at', 'direction' => 'desc']));

        $response->assertOk();
        $response->assertSeeInOrder([$bookB->title, $bookA->title]);
    }
}
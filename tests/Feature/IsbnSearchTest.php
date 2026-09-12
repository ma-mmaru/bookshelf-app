<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IsbnSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_isb_nコードを指定して外部_ap_iから書籍情報を取得できること(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'totalItems' => 1,
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'Google Booksテスト書籍',
                            'authors' => ['Google著者'],
                            'publishedDate' => '2024-01-01',
                            'description' => 'Google Books API経由でのテスト概要',
                            'imageLinks' => [
                                'thumbnail' => 'https://books.google.com/books/content?id=test&printsec=frontcover&img=1',
                            ],
                            'industryIdentifiers' => [
                                [
                                    'type' => 'ISBN_13',
                                    'identifier' => '9784798160000',
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)
            ->get(route('books.isbn', ['isbn' => '9784798160000']));

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Google Booksテスト書籍']);
    }
}

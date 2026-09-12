<?php

namespace Tests\Unit;

use App\Enums\ReadingPlanStatus;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ResourceAndEnumTest extends TestCase
{
    use RefreshDatabase;

    public function test_読書計画ステータスEnumの値が正常に取得できること(): void
    {
        $cases = ReadingPlanStatus::cases();
        $this->assertNotEmpty($cases);

        foreach ($cases as $case) {
            $this->assertIsString($case->value);
        }
    }

    public function test_レビューResourceが正しい配列フォーマットに変換されること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'comment' => 'テストレビュー',
            'rating'  => 5,
        ]);

        $resource = new ReviewResource($review);
        $arrayData = $resource->toArray(new Request());

        $this->assertIsArray($arrayData);
        $this->assertEquals($review->id, $arrayData['id']);
        $this->assertEquals('テストレビュー', $arrayData['comment']);
    }
}
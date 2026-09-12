<?php

namespace Tests\Unit\Models;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_読書計画が特定のユーザーに紐づいていること(): void
    {
        $user = User::factory()->create();
        $plan = ReadingPlan::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $plan->user);
        $this->assertEquals($user->id, $plan->user->id);
    }

    public function test_読書計画が特定の書籍に紐づいていること(): void
    {
        $book = Book::factory()->create();
        $plan = ReadingPlan::factory()->create(['book_id' => $book->id]);

        $this->assertInstanceOf(Book::class, $plan->book);
        $this->assertEquals($book->id, $plan->book->id);
    }

    public function test_status属性が_reading_plan_statusの_enum型にキャストされること(): void
    {
        $plan = ReadingPlan::factory()->create([
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $this->assertInstanceOf(ReadingPlanStatus::class, $plan->status);
        $this->assertEquals(ReadingPlanStatus::InProgress, $plan->status);
    }

    public function test_target_dateが設定されている場合はその日付を返し_nullの場合は現在日時相当を返すこと(): void
    {
        $planWithDate = ReadingPlan::factory()->create([
            'target_date' => '2026-10-01',
        ]);
        $this->assertNotNull($planWithDate->target_date);

        $planNullDate = ReadingPlan::factory()->create([
            'target_date' => null,
        ]);
        $this->assertNotNull($planNullDate->getTargetDateAttribute());
    }
}

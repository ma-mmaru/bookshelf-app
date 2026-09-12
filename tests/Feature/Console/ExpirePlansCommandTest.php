<?php

namespace Tests\Feature\Console;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpirePlansCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_期限切れの読書計画が自動的に失効ステータスに変更されること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $expiredPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => now()->subDay(),
        ]);

        $this->artisan('reading-plans:process-daily')
            ->assertExitCode(0);

        $this->assertDatabaseHas('reading_plans', [
            'id' => $expiredPlan->id,
            'status' => ReadingPlanStatus::Overdue->value,
        ]);
    }
}

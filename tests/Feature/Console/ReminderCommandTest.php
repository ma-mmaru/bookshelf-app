<?php

namespace Tests\Feature\Console;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanDueDateNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_期限が近い読書計画を持つユーザーにリマインダーが処理されること(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $this->artisan('reading-plans:process-daily')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $user,
            ReadingPlanDueDateNotification::class
        );
    }
}

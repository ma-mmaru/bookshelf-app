<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanDueDateNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_読書計画のリマインダー通知メッセージが正常に生成されること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $notification = new ReadingPlanDueDateNotification(
            'タイトル',
            '本文',
            'timing_value'
        );

        $arrayData = $notification->toArray($user);
        $this->assertIsArray($arrayData);
    }
}

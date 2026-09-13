<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_自分の読書計画を更新・削除できること()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Planned,
        ]);

        $response = $this->actingAs($user)->put(route('reading-plans.update', $plan), [
            'status' => ReadingPlanStatus::InProgress->value,
            'target_date' => now()->addDays(7)->format('Y-m-d'),
        ]);
        $response->assertStatus(302);

        $response = $this->actingAs($user)->delete(route('reading-plans.destroy', $plan));
        $response->assertStatus(302);
    }

    public function test_他人の読書計画の更新は拒否されること()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create();

        $otherPlan = ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->put(route('reading-plans.update', $otherPlan), [
            'status' => ReadingPlanStatus::Completed->value,
        ]);
        $response->assertStatus(403);
    }
}

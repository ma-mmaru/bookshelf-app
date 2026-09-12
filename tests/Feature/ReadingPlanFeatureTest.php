<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_読書計画の新規登録と一覧表示ができること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('reading-plans.store'), [
            'book_id' => $book->id,
            'target_date' => now()->addWeek()->format('Y-m-d'),
            'status' => ReadingPlanStatus::Planned->value,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_読書計画の目標期日（期限）を変更できること(): void
    {
        $user = User::factory()->create();
        $plan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'target_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $newDate = now()->addMonth()->format('Y-m-d');

        $response = $this->actingAs($user)->put(route('reading-plans.update', $plan), [
            'target_date' => $newDate,
            'status' => $plan->status->value,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'target_date' => $newDate,
        ]);
    }
}

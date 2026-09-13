<?php

namespace Tests\Feature;

use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_他人の読書計画を更新しようとした場合403エラーになること(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $plan = ReadingPlan::factory()->create([
            'user_id' => $userA->id,
        ]);

        $response = $this->actingAs($userB)
            ->putJson("/api/v1/reading-plans/{$plan->id}", [
                'status' => 'completed',
            ]);

        $response->assertStatus(403);
    }

    public function test_他人の読書計画を削除しようとした場合403エラーになること(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $plan = ReadingPlan::factory()->create([
            'user_id' => $userA->id,
        ]);

        $response = $this->actingAs($userB)
            ->deleteJson("/api/v1/reading-plans/{$plan->id}");

        $response->assertStatus(403);
    }

    public function test_存在しない読書計画を取得しようとした場合404エラーになること(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/v1/reading-plans/999999');

        $response->assertStatus(404);
    }

    public function test_未ログイン状態では読書計画一覧にアクセスできないこと(): void
    {
        $response = $this->getJson('/api/v1/reading-plans');

        $response->assertStatus(401);
    }
}

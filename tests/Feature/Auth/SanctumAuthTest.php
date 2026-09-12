<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SanctumAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_sanctumトークンによる_ap_i認証と保護ルートへのアクセスができること(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/v1/user')->assertUnauthorized();

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/v1/user');
        $response->assertOk();
        $response->assertJsonPath('id', $user->id);
    }
}

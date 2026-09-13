<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $ユーザー;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ユーザー = User::factory()->create();
    }

    public function test_ジャンル一覧画面を表示できる(): void
    {
        $レスポンス = $this->actingAs($this->ユーザー)
            ->get(route('genres.index'));

        $レスポンス->assertOk();
        $レスポンス->assertViewIs('genres.index');
    }

    public function test_ジャンル詳細画面を表示できる(): void
    {
        $ジャンル = Genre::factory()->create();

        $レスポンス = $this->actingAs($this->ユーザー)
            ->get(route('genres.show', $ジャンル));

        $レスポンス->assertOk();
        $レスポンス->assertViewIs('genres.show');
    }

    public function test_ジャンル新規作成画面を表示できる(): void
    {
        $レスポンス = $this->actingAs($this->ユーザー)
            ->get(route('genres.create'));

        $レスポンス->assertOk();
        $レスポンス->assertViewIs('genres.create');
    }

    public function test_ジャンルを正常に登録できる(): void
    {
        $送信データ = ['name' => '小説'];

        $レスポンス = $this->actingAs($this->ユーザー)
            ->post(route('genres.store'), $送信データ);

        $レスポンス->assertRedirect(route('genres.index'));
        $レスポンス->assertSessionHas('status', 'ジャンルを登録しました。');
        $this->assertDatabaseHas('genres', ['name' => '小説']);
    }

    public function test_ジャンル編集画面を表示できる(): void
    {
        $ジャンル = Genre::factory()->create();

        $レスポンス = $this->actingAs($this->ユーザー)
            ->get(route('genres.edit', $ジャンル));

        $レスポンス->assertOk();
        $レスポンス->assertViewIs('genres.edit');
    }

    public function test_ジャンルを正常に更新できる(): void
    {
        $ジャンル = Genre::factory()->create(['name' => '旧ジャンル名']);
        $更新データ = ['name' => '新ジャンル名'];

        $レスポンス = $this->actingAs($this->ユーザー)
            ->put(route('genres.update', $ジャンル), $更新データ);

        $レスポンス->assertRedirect(route('genres.index'));
        $レスポンス->assertSessionHas('status', 'ジャンル名を更新しました。');
        $this->assertDatabaseHas('genres', ['id' => $ジャンル->id, 'name' => '新ジャンル名']);
    }

    public function test_書籍が紐づいていないジャンルは削除できる(): void
    {
        $ジャンル = Genre::factory()->create();

        $レスポンス = $this->actingAs($this->ユーザー)
            ->delete(route('genres.destroy', $ジャンル));

        $レスポンス->assertRedirect(route('genres.index'));
        $レスポンス->assertSessionHas('status', 'ジャンルを削除しました。');
        $this->assertDatabaseMissing('genres', ['id' => $ジャンル->id]);
    }

    public function test_書籍が紐づいているジャンルは削除できずエラーになる(): void
    {
        $ジャンル = Genre::factory()->create();

        Book::factory()->create()->genres()->attach($ジャンル);

        $レスポンス = $this->actingAs($this->ユーザー)
            ->delete(route('genres.destroy', $ジャンル));

        $レスポンス->assertSessionHasErrors(['error' => '書籍が紐づいているジャンルは削除できません。']);
        $this->assertDatabaseHas('genres', ['id' => $ジャンル->id]);
    }
}

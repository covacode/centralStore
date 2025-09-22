<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_users()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/users', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email']
            ]);
    }

    public function test_can_show_user()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email']
            ]);
    }

    public function test_can_update_user()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'newpassword123'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/users/{$this->user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $updateData['name']);
    }

    public function test_can_delete_user()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/users/{$this->user->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', ['id' => $this->user->id]);
    }

    public function test_can_restore_user()
    {
        $this->user->delete();

        $this->assertSoftDeleted($this->user);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/users/restore/{$this->user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'deleted_at' => null
        ]);
    }
}

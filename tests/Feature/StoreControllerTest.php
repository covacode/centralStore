<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $store;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->store = Store::factory()->create(['user' => $this->user->id]);
    }

    public function test_can_list_stores()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/stores');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_store()
    {
        $storeData = [
            'name' => 'Test Store',
            'user' => $this->user->id
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/stores', $storeData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'user']
            ]);
    }

    public function test_can_show_store()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/stores/{$this->store->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'user']
            ]);
    }

    public function test_cannot_show_nonexistent_store()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/stores/9999");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'resource not found'
            ]);
    }

    public function test_can_update_store()
    {
        $updateData = [
            'name' => 'Updated Store Name',
            'user' => $this->user->id
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/stores/{$this->store->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $updateData['name']);
    }

    public function test_can_delete_store()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/stores/{$this->store->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('stores', ['id' => $this->store->id]);
    }

    public function test_can_restore_store()
    {
        // Primero borramos la tienda
        $this->store->delete();
        $this->assertSoftDeleted($this->store);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/stores/restore/{$this->store->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('stores', [
            'id' => $this->store->id,
            'deleted_at' => null
        ]);
    }

    public function test_cannot_restore_undeleted_store()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/stores/restore/{$this->store->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'bad request'
            ]);
    }
}

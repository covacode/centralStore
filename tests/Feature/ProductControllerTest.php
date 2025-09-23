<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
    }

    public function test_can_list_products()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_product()
    {
        $productData = [
            'name' => 'Test Product',
            'ean13' => '1234567890123',
            'description' => 'Test Description'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/products', $productData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'name', 'ean13', 'description']
            ]);
    }

    public function test_can_show_product()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/products/{$this->product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'ean13', 'description']
            ]);
    }

    public function test_cannot_show_nonexistent_product()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/products/9999");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'resource not found'
            ]);
    }

    public function test_can_update_product()
    {
        $updateData = [
            'name' => 'Updated Product Name',
            'ean13' => '1234567890123',
            'description' => 'Updated Description'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/products/{$this->product->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', $updateData['name'])
            ->assertJsonPath('data.description', $updateData['description']);
    }

    public function test_can_delete_product()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/products/{$this->product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $this->product->id]);
    }

    public function test_can_restore_product()
    {
        $this->product->delete();
        $this->assertSoftDeleted($this->product);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/products/restore/{$this->product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'deleted_at' => null
        ]);
    }

    public function test_cannot_restore_undeleted_product()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/products/restore/{$this->product->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'bad request'
            ]);
    }
}

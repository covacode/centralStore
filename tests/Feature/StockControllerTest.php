<?php

namespace Tests\Feature;

use App\Http\Resources\ProductResource;
use App\Http\Resources\StoreResource;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $store;
    protected $product;
    protected $stock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->store = Store::factory()->create(['user' => $this->user->id]);
        $this->product = Product::factory()->create();
        $this->stock = Stock::factory()->create([
            'store' => $this->store->id,
            'product' => $this->product->id,
            'available_quantity' => 10,
            'reserved_quantity' => 0,
            'total_quantity' => 10
        ]);
    }

    public function test_can_list_stocks()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/stocks');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_post_stocks_by_store()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/stocks/byStore/{$this->store->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_post_stocks_by_product()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/stocks/byProduct/{$this->product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_reserve_stock()
    {
        $reserveData = [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'reserved_quantity' => 5
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/stocks/reserve', $reserveData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('stocks', [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'available_quantity' => 5,
            'reserved_quantity' => 5,
            'total_quantity' => 10
        ]);

        $response->assertJsonStructure([
            'data' => [
                'store',
                'product',
                'available_quantity',
                'reserved_quantity',
                'total_quantity'
            ]
        ]);
    }

    public function test_cannot_reserve_more_than_available()
    {
        $reserveData = [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'reserved_quantity' => 20
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/stocks/reserve', $reserveData);

        $response->assertStatus(400)
            ->assertJson([
                'code' => 400,
                'success' => false,
                'message' => 'insufficient available stock to reserve the requested quantity',
                'errors' => [
                    'available_quantity' => 10,
                    'requested_quantity' => 20
                ]
            ]);
    }

    public function test_can_release_stock()
    {
        // First reserve some stock
        $this->stock->update([
            'available_quantity' => 5,
            'reserved_quantity' => 5
        ]);

        $releaseData = [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'available_quantity' => 3
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/stocks/release', $releaseData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('stocks', [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'available_quantity' => 8,
            'reserved_quantity' => 2
        ]);

        $response->assertJsonStructure([
            'data' => [
                'store',
                'product',
                'available_quantity',
                'reserved_quantity',
                'total_quantity'
            ]
        ]);
    }

    public function test_can_sell_stock()
    {
        // First reserve some stock
        $this->stock->update([
            'available_quantity' => 5,
            'reserved_quantity' => 5,
            'total_quantity' => 10
        ]);

        $sellData = [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'quantity_ToSell' => 3
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/stocks/sell', $sellData);

        $response->assertStatus(200);

        // Verifica el estado final del stock después de la venta
        $this->assertDatabaseHas('stocks', [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'available_quantity' => 2,
            'reserved_quantity' => 5,
            'total_quantity' => 7
        ]);

        $response->assertJsonStructure([
            'data' => [
                'store',
                'product',
                'available_quantity',
                'reserved_quantity',
                'total_quantity'
            ]
        ]);
    }

    public function test_can_refund_stock()
    {
        // First reserve some stock
        $this->stock->update([
            'available_quantity' => 5,
            'reserved_quantity' => 5,
            'total_quantity' => 10
        ]);

        $refundData = [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'quantity_ToRefund' => 3
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/stocks/refund', $refundData);

        $response->assertStatus(200);

        // Verifica el estado final del stock después de la venta
        $this->assertDatabaseHas('stocks', [
            'store' => $this->store->id,
            'product' => $this->product->id,
            'available_quantity' => 8,
            'reserved_quantity' => 5,
            'total_quantity' => 13
        ]);

        $response->assertJsonStructure([
            'data' => [
                'store',
                'product',
                'available_quantity',
                'reserved_quantity',
                'total_quantity'
            ]
        ]);
    }
}

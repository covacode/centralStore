<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Spatie\Activitylog\Models\Activity;

class ApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);
    }

    public function test_health_check()
    {
        $response = $this->getJson('/api/auth/healthCheck');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'API is healthy'
            ]);
    }

    public function test_user_can_login()
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'token'
                ]
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'invalid credentials'
            ]);
    }

    public function test_user_can_logout()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'logged out'
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_can_fetch_audit_logs()
    {
        Sanctum::actingAs($this->user);

        // Create some activity logs
        activity()
            ->causedBy($this->user)
            ->log('Test activity');

        $response = $this->postJson('/api/audit', ['limit' => 10], ['Accept' => 'application/json']);

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'App\Models\User',
            'subject_id' => $this->user->id,
            'subject_type' => 'App\Models\User',
            'causer_id' => null,
            'causer_type' => null
        ]);

        $response->assertJsonStructure([
            'code',
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'log_name',
                    'description',
                    'created_at'
                ]
            ]
        ]);
    }

    public function test_can_fetch_audit_detail()
    {
        Sanctum::actingAs($this->user);

        // Create an activity log for user
        activity()
            ->performedOn($this->user)
            ->log('Test activity');

        $data = [
            'log_name' => 'User',
            'subject_id' => $this->user->id,
            'limit' => 10
        ];

        $response = $this->postJson('/api/audit/detail', $data, ['Accept' => 'application/json']);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'App\Models\User',
            'subject_id' => $this->user->id,
            'subject_type' => 'App\Models\User',
            'causer_id' => null,
            'causer_type' => null
        ]);

        $response->assertJsonStructure([
            'code',
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'log_name',
                    'description',
                    'created_at'
                ]
            ]
        ]);
    }
}

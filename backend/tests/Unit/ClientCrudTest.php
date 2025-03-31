<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Client;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $headers;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and get token
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $this->headers = ['Authorization' => "Bearer {$this->token}"];

        // Create a test client for this user
        $this->client = Client::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'company' => 'Test Company'
        ]);
    }

    public function test_client_can_be_created()
    {
        $response = $this->postJson('/api/clients', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '1234567890',
            'company' => 'Test Company',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'email']);
    }

    public function test_client_list_can_be_retrieved()
    {
        // Create another client for this user to test list
        Client::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Another Client'
        ]);

        $response = $this->getJson('/api/clients', $this->headers);

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'name' => 'Test Client',
                'email' => 'test@example.com'
            ]);
    }

    public function test_client_can_be_updated()
    {
        $updatedData = [
            'name' => 'Updated Client Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210'
        ];

        $response = $this->putJson("/api/clients/{$this->client->id}", $updatedData, $this->headers);

        $response->assertStatus(200)
            ->assertJson($updatedData);

        $this->assertDatabaseHas('clients', [
            'id' => $this->client->id,
            'name' => 'Updated Client Name',
            'email' => 'updated@example.com',
            'user_id' => $this->user->id
        ]);
    }

    public function test_client_can_be_deleted()
    {
        $response = $this->deleteJson("/api/clients/{$this->client->id}", [], $this->headers);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('clients', [
            'id' => $this->client->id
        ]);
    }

    public function test_user_cannot_update_other_users_client()
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->putJson("/api/clients/{$otherClient->id}", [
            'name' => 'Trying to Update'
        ], $this->headers);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_other_users_client()
    {
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->deleteJson("/api/clients/{$otherClient->id}", [], $this->headers);

        $response->assertStatus(403);

        $this->assertDatabaseHas('clients', [
            'id' => $otherClient->id
        ]);
    }
}

<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Meeting;
use App\Models\Client;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeetingCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $headers;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $this->headers = ['Authorization' => "Bearer {$this->token}"];
        $this->client = Client::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_meeting_can_be_created()
    {
        $response = $this->postJson('/api/meetings', [
            'client_id' => $this->client->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'notes' => 'Discuss project details',
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'client_id', 'scheduled_at', 'notes']);
    }

    public function test_meeting_can_be_read()
    {
        $meeting = Meeting::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id
        ]);

        $response = $this->getJson("/api/meetings/{$meeting->id}", $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $meeting->id,
                'client_id' => $meeting->client_id,
                'scheduled_at' => $meeting->scheduled_at->toJSON(),
                'notes' => $meeting->notes,
            ]);
    }

    public function test_meeting_can_be_updated()
    {
        $meeting = Meeting::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id
        ]);

        $response = $this->putJson("/api/meetings/{$meeting->id}", [
            'notes' => 'Updated meeting notes',
        ], $this->headers);

        $response->assertStatus(200)
            ->assertJson(['notes' => 'Updated meeting notes']);

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'notes' => 'Updated meeting notes',
        ]);
    }

    public function test_meeting_can_be_deleted()
    {
        $meeting = Meeting::factory()->create([
            'user_id' => $this->user->id,
            'client_id' => $this->client->id
        ]);

        $response = $this->deleteJson("/api/meetings/{$meeting->id}", [], $this->headers);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('meetings', [
            'id' => $meeting->id,
        ]);
    }
}

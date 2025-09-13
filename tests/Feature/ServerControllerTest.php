<?php

namespace Tests\Feature;

use App\Models\Server;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServerControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_servers()
    {
        Server::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/servers');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'provider', 'ip_address', 'status', 'cpu_cores', 'ram_mb', 'storage_gb', 'created_at', 'updated_at'],
                ],
                'meta' => ['current_page', 'from', 'last_page', 'per_page', 'to', 'total'],
                'links' => ['first', 'last', 'prev', 'next'],
            ]);
    }

    #[Test]
    public function it_can_create_a_server()
    {
        $payload = [
            'name' => 'My Server',
            'provider' => 'vultr',
            'ip_address' => '192.168.1.1',
            'status' => 'active',
            'cpu_cores' => 4,
            'ram_mb' => 8192,
            'storage_gb' => 200,
        ];

        $response = $this->postJson('/api/v1/servers', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'My Server']);

        $this->assertDatabaseHas('servers', ['name' => 'My Server']);
    }

    #[Test]
    public function it_fails_to_create_server_with_duplicate_ip_or_provider_name()
    {
        // First, create an existing server
        $existingServer = Server::factory()->create([
            'name' => 'Server One',
            'ip_address' => '192.168.1.1',
            'provider' => 'aws',
            'status' => 'active',
            'cpu_cores' => 2,
            'ram_mb' => 4096,
            'storage_gb' => 100,
        ]);

        // Attempt to create a server with same IP
        $payloadDuplicateIp = [
            'name' => 'Server Two',
            'ip_address' => '192.168.1.1',
            'provider' => 'gcp',
            'status' => 'active',
            'cpu_cores' => 2,
            'ram_mb' => 4096,
            'storage_gb' => 100,
        ];

        $response = $this->postJson('/api/v1/servers', $payloadDuplicateIp);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ip_address']);

        // Attempt to create a server with same provider + name
        $payloadDuplicateProviderName = [
            'name' => 'Server One',
            'ip_address' => '192.168.1.2',
            'provider' => 'aws',
            'status' => 'active',
            'cpu_cores' => 2,
            'ram_mb' => 4096,
            'storage_gb' => 100,
        ];

        $response = $this->postJson('/api/v1/servers', $payloadDuplicateProviderName);
        $response->dump();
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function it_can_show_a_server()
    {
        $server = Server::factory()->create();

        $response = $this->getJson("/api/v1/servers/{$server->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $server->id]);
    }

    #[Test]
    public function it_returns_404_if_server_not_found()
    {
        $response = $this->getJson('/api/v1/servers/99999');

        $response->assertStatus(404)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Server not found.',
            ]);
    }

    #[Test]
    public function it_can_update_a_server()
    {
        $server = Server::factory()->create();

        $payload = [
            'name' => 'Updated Server',
            'provider' => 'aws',
            'ip_address' => '192.168.1.2',
            'status' => 'inactive',
            'cpu_cores' => 4,
            'ram_mb' => 8192,
            'storage_gb' => 200,
        ];

        $response = $this->putJson("/api/v1/servers/{$server->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Server']);

        $this->assertDatabaseHas('servers', [
            'name' => 'Updated Server',
            'provider' => 'aws',
            'ip_address' => '192.168.1.2',
            'status' => 'inactive',
            'cpu_cores' => 4,
            'ram_mb' => 8192,
            'storage_gb' => 200,
        ]);
    }

    #[Test]
    public function it_can_delete_a_server()
    {
        $server = Server::factory()->create();

        $response = $this->deleteJson("/api/v1/servers/{$server->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('servers', ['id' => $server->id]);
    }
}

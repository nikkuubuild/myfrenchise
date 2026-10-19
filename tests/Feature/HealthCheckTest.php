<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test the health check endpoint returns 200 and valid json payload.
     */
    public function test_health_check_endpoint_reports_healthy_and_connected_database(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'app' => 'Franchise Dekho',
                'database' => 'connected',
            ]);

        $response->assertJsonStructure([
            'status',
            'app',
            'environment',
            'database',
            'timestamp',
        ]);
    }
}

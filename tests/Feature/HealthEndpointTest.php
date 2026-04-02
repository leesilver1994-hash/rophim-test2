<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    public function test_health_endpoint_responds_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk();
    }
}

<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ApiTest extends TestCase
{
    /**
     * Test if API v1 exists.
     *
     * @return void
     */
    public function test_existing_api_version(): void
    {
        $response = $this->get('/v1');

        $response->assertStatus(200);
        $response->assertJson([
          'message' => 'Successfully connected!',
          'api_version' => 'v1'
        ]);
    }

    /**
     * Test an invalid endpoint for an existing API version
     *
     * @return void
     */
    public function test_invalid_endpoint(): void
    {
        $response = $this->get('/v1/invalid-endpoint');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Invalid endpoint.',
            'api_version' => 'v1'
        ]);
    }

    /**
     * Test a valid, but non-existing API version
     *
     * @return void
     */
    public function test_non_existing_api_version(): void
    {
        $api_version = rand(10,99);
        $response = $this->get("/v$api_version");

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'API version does not exist.',
            'api_version' => "v$api_version"
        ]);
    }

    /**
     * Test if Api V1 exists.
     *
     * @return void
     */
    public function test_invalid_api_version(): void
    {
        $response = $this->get('/api-version');

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Invalid endpoint.',
            'api_version' => null
        ]);
    }
}

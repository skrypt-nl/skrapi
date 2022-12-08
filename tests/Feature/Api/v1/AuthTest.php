<?php

namespace Tests\Feature\Api\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    /**
     * Test a failed login attempt
     *
     * @return void
     */
    public function testFailedLogin(): void
    {
        $request = [
            'email' => $this->user->email,
            'password' => 'false'
        ];

        $response = $this->post('/v1/login', $request);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid login credentials.'
        ]);
    }

    /**
     * Test a successful login attempt
     *
     * @return void
     */
    public function testSuccessfulLogin(): void
    {
        $request = [
            'email' => $this->user->email,
            'password' => 'secret'
        ];

        $response = $this->post('/v1/login', $request);

        $response->assertStatus(200);

        $response->assertJson([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]
        ]);

        $this->assertNotNull($response['access_token']);
    }

    /**
     * Test a successful logout attempt
     *
     * @return void
     */
    public function testSuccessfulLogout(): void
    {
        $request = [
            'email' => $this->user->email,
            'password' => 'secret'
        ];

        $loginResponse = $this->post('/v1/login', $request);

        $loginResponse->assertStatus(200);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $loginResponse['access_token'],
        ])->post('/v1/logout', $request);

        $response->assertStatus(200);

        $response->assertJson(['message' => 'You have been successfully logged out!']);
    }

    /**
     * Test a failed logout attempt
     *
     * @return void
     */
    public function testFailedLogout(): void
    {
        $response = $this->post('/v1/logout');
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}

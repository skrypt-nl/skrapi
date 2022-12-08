<?php

namespace Tests\Feature\Api\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use WithFaker;

    private User $user;
    private string $userAccessToken;
    private User $admin;
    private string $adminAccessToken;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->user->assignRole('User');
        $this->userAccessToken = $this->user->createToken('testingToken')->accessToken;

        $this->admin = User::factory()->createOne();
        $this->admin->assignRole('Admin');
        $this->adminAccessToken = $this->admin->createToken('testingToken')->accessToken;
    }

    /**
     * Test if a regular user can only view itself.
     *
     * @return void
     */
    public function test_if_users_can_only_view_themselves(): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->userAccessToken,
        ];

        // This user
        $this
            ->withHeaders($headers)
            ->get('/v1/users/' . $this->user->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ]
            ]);

        // Single other user
        $this
            ->withHeaders($headers)
            ->get('/v1/users/' . $this->admin->id)
            ->assertStatus(403);

        // Multiple other users
        $this
            ->withHeaders($headers)
            ->get('/v1/users')
            ->assertStatus(403);
    }

    /**
     * Test if an admin can view all users.
     *
     * @return void
     */
    public function test_if_admins_can_view_all_users(): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->adminAccessToken,
        ];

        // This user
        $this
            ->withHeaders($headers)
            ->get('/v1/users/' . $this->admin->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->admin->id,
                    'name' => $this->admin->name,
                ]
            ]);

        // Single other user
        $this
            ->withHeaders($headers)
            ->get('/v1/users/' . $this->user->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ]
            ]);

        // Multiple other user
        $this
            ->withHeaders($headers)
            ->get('/v1/users')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }


    /**
     * Test if a regular user cannot create new users.
     *
     * @return void
     */
    public function test_if_users_cannot_create_new_users(): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->userAccessToken,
        ];

        $newUser = User::factory()->makeOne();
        $request = [
            'name' => $newUser->name,
            'email' => $newUser->email,
            'password' => $newUser->password,
        ];

        $this
            ->withHeaders($headers)
            ->post('/v1/users', $request)
            ->assertStatus(403);
    }

    /**
     * Test if an admin can create new users.
     *
     * @return void
     */
    public function test_if_admins_can_create_new_users(): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->adminAccessToken,
        ];

        $newUser = User::factory()->makeOne();
        $request = [
            'name' => $newUser->name,
            'email' => $newUser->email,
            'password' => $newUser->password,
        ];

        $this
            ->withHeaders($headers)
            ->post('/v1/users', $request)
            ->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => $newUser->name,
            'email' => $newUser->email,
        ]);

        User::whereEmail($newUser->email)->forceDelete();
    }

    /**
     * Test if regular users can only edit themselves, and not others.
     *
     * @return void
     */
    public function test_if_users_can_only_update_themselves(): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->userAccessToken,
        ];

        $newName = $this->faker->name();

        $request = [
            'name' => $newName,
        ];

        // Check if User cannot edit another User
        $this
            ->withHeaders($headers)
            ->patch('/v1/users/' . $this->admin->id, $request)
            ->assertStatus(403);

        // Check if User can edit itself
        $this
            ->withHeaders($headers)
            ->patch('/v1/users/' . $this->user->id, $request)
            ->assertStatus(200);

        // Make sure the name has been updated
        $this->assertDatabaseHas('users', [
            'name' => $newName,
            'email' => $this->user->email,
        ]);
    }

    /**
     * Test if regular users can only edit themselves, and not others.
     *
     * @return void
     */
    public function test_if_admins_can_update_any_user(): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->adminAccessToken,
        ];

        $newUserName = $this->faker->name();
        $newAdminName = $this->faker->name();

        // Check if Admin can edit another User
        $this
            ->withHeaders($headers)
            ->patch('/v1/users/' . $this->user->id, ['name' => $newUserName])
            ->assertStatus(200);

        // Make sure the Users name has been updated
        $this->assertDatabaseHas('users', [
            'name' => $newUserName,
            'email' => $this->user->email,
        ]);

        // Check if Admin can edit itself
        $this
            ->withHeaders($headers)
            ->patch('/v1/users/' . $this->admin->id, ['name' => $newAdminName])
            ->assertStatus(200);

        // Make sure the Admins name has been updated
        $this->assertDatabaseHas('users', [
            'name' => $newAdminName,
            'email' => $this->admin->email,
        ]);
    }

    /**
     * Test if regular users are not allowed to delete any users.
     *
     * @return void
     */
    public function test_if_users_cant_delete_users(): void
    {
        $newUser = User::factory()->createOne();

        $this
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->userAccessToken,
            ])
            ->delete('/v1/users/' . $newUser->id)
            ->assertStatus(403);

        $this->assertModelExists($newUser);
        $this->assertNotSoftDeleted($newUser);

        $this
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->userAccessToken,
            ])
            ->delete('/v1/users/' . $newUser->id . '?force=true')
            ->assertStatus(403);

        $this->assertModelExists($newUser);
        $this->assertNotSoftDeleted($newUser);

        $newUser->forceDelete();
    }

    /**
     * Test if admins can soft-delete users
     *
     * @return void
     */
    public function test_if_admins_can_soft_delete_users(): void
    {
        $newUser = User::factory()->createOne();

        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->adminAccessToken,
            ])
            ->delete('/v1/users/' . $newUser->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted($newUser);

        $newUser->forceDelete();
    }

    /**
     * Test if admins can force-delete users
     *
     * @return void
     */
    public function test_if_admins_can_force_delete_users(): void
    {
        $newUser = User::factory()->createOne();

        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->adminAccessToken,
            ])
            ->delete('/v1/users/' . $newUser->id . '?force=true');

        $response->assertStatus(200);

        $this->assertModelMissing($newUser);
    }


    /**
     * Test if regular users cannot restore soft-deleted users
     *
     * @return void
     */
    public function test_if_users_cant_restore_users(): void
    {
        $newUser = User::factory()->createOne();
        $newUser->delete();

        $this->assertSoftDeleted($newUser);

        $this
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->userAccessToken,
            ])
            ->post('/v1/users/' . $newUser->id . '/restore')
            ->assertStatus(403);

        $this->assertModelExists($newUser);
        $this->assertSoftDeleted($newUser);

        $newUser->forceDelete();
    }

    /**
     * Test if admins can restore soft-deleted users
     *
     * @return void
     */
    public function test_if_admins_can_restore_users(): void
    {
        $newUser = User::factory()->createOne();
        $newUser->delete();

        $this->assertSoftDeleted($newUser);

        $this
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->adminAccessToken,
            ])
            ->post('/v1/users/' . $newUser->id . '/restore')
            ->assertStatus(200);

        $this->assertModelExists($newUser);
        $this->assertNotSoftDeleted($newUser);

        $newUser->forceDelete();
    }
}

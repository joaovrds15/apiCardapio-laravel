<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @coversNothing
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_valid_credentials_can_login()
    {
        $user = User::factory()->create();
        $userData = [
            'username' => $user['username'],
            //Passar secret teste para env
            'password' => 'secret@123',
        ];

        $response = $this
            ->postJson('api/auth/login', $userData);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_user_with_invalid_credentials_cant_login()
    {
        $user = User::factory()->create();
        $userData = [
            'username' => $user['username'],
            //Passar secret teste para env
            'password' => 'secret123',
        ];

        $response = $this
            ->postJson('api/auth/login', $userData);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_with_valid_data_can_register()
    {
        $userData = [
            'username' => 'nathan123',
            //Passar secret teste para env
            'password' => 'secret@123',
            're_password' => 'secret@123',
        ];

        $response = $this
            ->postJson('api/auth/register', $userData);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_user_with_invalid_data_cant_register()
    {
        $userData = [
            'username' => 'nathan123',
            //Passar secret teste para env
            'password' => 'secret@123',
            're_password' => 'secret@124',
        ];

        $response = $this
            ->postJson('api/auth/register', $userData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_user_with_username_already_in_use_cant_register()
    {
        $userData = [
            'username' => 'nathan123',
            //Passar secret teste para env
            'password' => 'secret@123',
            're_password' => 'secret@123',
        ];
        $this->postJson('api/auth/register', $userData);

        $response = $this
            ->postJson('api/auth/register', $userData);

        $response->assertStatus(Response::HTTP_CONFLICT);
    }

    public function test_logged_in_user_can_logout()
    {
        $user = User::factory()->create();
        $userData = [
            'username' => $user['username'],
            //Passar secret teste para env
            'password' => 'secret@123',
        ];
        $response = $this->postJson('api/auth/login', $userData);
        $token = $response->json('access_token');
        $response = $this
            ->withHeaders([
                'Authorization' => 'Bearer '.$token,
            ])
            ->postJson('api/auth/logout', $userData);

        $response->assertStatus(Response::HTTP_OK);
    }
}

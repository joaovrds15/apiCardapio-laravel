<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Support\RefreshFlow;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_with_valid_credentials_can_login()
    {
       $user = User::factory()->create();
        $userData = [
            "username" => $user['username'],
            //Passar secret teste para env
            "password" => 'secret@123',
        ];

        $response = $this
            ->postJson('api/auth/login',$userData);
        
        $response->assertStatus(200);
    }

    public function test_user_with_invalid_credentials_cant_login()
    {
       $user = User::factory()->create();
        $userData = [
            "username" => $user['username'],
            //Passar secret teste para env
            "password" => 'secret123',
        ];

        $response = $this
            ->postJson('api/auth/login',$userData);

        $response->assertStatus(401);
    }

    public function test_user_with_valid_data_can_register()
    {
       
        $userData = [
            "username" => 'nathan123',
            //Passar secret teste para env
            "password" => 'secret@123',
            "re_password" => 'secret@123',
        ];

        $response = $this
            ->postJson('api/auth/register',$userData);

        $response->assertStatus(201);
    }

    public function test_user_with_invalid_data_cant_register()
    {
       
        $userData = [
            "username" => 'nathan123',
            //Passar secret teste para env
            "password" => 'secret@123',
            "re_password" => 'secret@124',
        ];

        $response = $this
            ->postJson('api/auth/register',$userData);

        $response->assertStatus(400);
    }

    public function test_user_with_username_already_in_use_cant_register()
    {
       
        $userData = [
            "username" => 'nathan123',
            //Passar secret teste para env
            "password" => 'secret@123',
            "re_password" => 'secret@123',
        ];
        $this->postJson('api/auth/register',$userData);

        $response = $this
            ->postJson('api/auth/register',$userData);

        $response->assertStatus(409);
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

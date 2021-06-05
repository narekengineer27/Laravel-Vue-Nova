<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoginTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        Artisan::call('passport:install', ['-vvv' => true]);
    }

    /** @test */
    public function a_user_can_log_in()
    {
        $user = factory(User::class)->create();
        $credentials = ['email' => $user->email, 'password' => 'secret'];

        /**
         * Login
         */
        $response = $this->json('POST', '/api/v1/login', $credentials);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);

    }

    /** @test */
    public function a_user_can_logout()
    {
        $user = factory(User::class)->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'secret'
        ];

        /**
         * Login
         */
        $response = $this->json('POST', '/api/v1/login', $credentials);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);

        $token = $response->json('access_token');

        $response = $this->json('GET', '/api/v1/logout', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }
}

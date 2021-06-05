<?php

namespace Tests\Feature;

use App\Models\User;
use Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    /** @test */
    public function a_user_can_register_via_email()
    {
        $user = make('App\Models\User', [
            'email_verified_at' => null,
        ]);

        unset($user['phone_number']);

        $this->json('POST', '/api/v1/register', $user->toArray())
            ->assertSee('access_token')
            ->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' =>  $user->email
        ]);
    }

    /** @test */
    public function a_user_can_register_via_phone()
    {
        $user = make('App\Models\User', [
            'email' => null,
            'email_verified_at' => null,
            'phone_number' => '+15005550006'
        ]);

        unset($user['email']);

        $response = $this->withExceptionHandling()
            ->json('POST', '/api/v1/register', $user->toArray())
            ->assertSee('access_token')
            ->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'phone_number' =>  $user->phone_number
        ]);
    }

    /**
     * Test with email that already has been taken
     */
    public function test_against_repeat_email_address_registration()
    {
        $email = $this->faker->email;

        $userOne = create(User::class, [
           'email' => $email
        ]);

        $userTwo = make(User::class, [
            'email' => $email
        ]);

        $response = $this
            ->withExceptionHandling()
            ->json('POST', '/api/v1/register', $userTwo->toArray())
            ->assertStatus(422);
    }

    /**
     * Test with phone that already has been taken
     */
    public function testPhoneError()
    {
        $phoneNumber = $this->faker->phoneNumber;

        $userOne = create(User::class, [
            'phone_number' => $phoneNumber
        ]);

        unset($userOne['email']);

        $userTwo = make(User::class, [
            'phone_number' => $phoneNumber
        ]);

        unset($userTwo['email']);


        $response = $this->withExceptionHandling()
            ->json('POST', '/api/v1/register', $userTwo->toArray())
            ->assertStatus(422)
            ->assertJsonStructure([
                'data' => [
                    'errors'
                ]
            ]);
    }
}

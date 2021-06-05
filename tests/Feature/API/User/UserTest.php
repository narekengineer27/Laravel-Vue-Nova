<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_updated()
    {
        $user   = factory(\App\Models\User::class)->create();
        Passport::actingAs($user);

        $params = [
            'email' => 'updated@test.com',
        ];
        $response = $this->json('POST', "/api/v1/users", $params);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email'      => 'updated@test.com',
        ]);
    }

    /** @test */
    public function a_user_can_be_deleted()
    {
        factory(\App\Models\User::class, 2)->create();
        $user   = factory(\App\Models\User::class)->create();
        Passport::actingAs($user);

        $params = [
            'email' => 'updated@test.com',
        ];
        $response = $this->json('DELETE', "/api/v1/users", $params);
        $response->assertStatus(200);
        $this->assertCount(2, User::all());
    }

    /** @test */
    public function users_can_be_listed()
    {
        $user   = factory(\App\Models\User::class)->create();
        Passport::actingAs($user);
        $response = $this->json('GET', "/api/v1/users");
        $response->assertStatus(200);
        $this->assertEquals($user->email, json_decode($response->getContent())->data->email);
    }

}

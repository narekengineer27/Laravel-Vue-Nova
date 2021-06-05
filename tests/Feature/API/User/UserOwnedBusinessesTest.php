<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/01/19
 * Time: 11:29 PM
 */

namespace Tests\Feature\API\User;


use App\Models\Business;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserOwnedBusinessesTest extends TestCase
{
    public function testGetOwnedBusinesses()
    {
        $user       = factory(\App\Models\User::class)->create();
        $businesses = factory(Business::class, 2)->create(['user_id' => $user->getKey()]);

        Passport::actingAs($user);

        $response = $this->json('GET', '/api/v1/user-owned-businesses')
            ->assertStatus(200);

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(array_key_exists('cover_photo', $content['data'][0]));
        $this->assertTrue(array_key_exists('avatar', $content['data'][0]));

    }
}

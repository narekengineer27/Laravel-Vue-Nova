<?php

namespace Tests\Feature\API\User\Bookmark;

use App\Models\User;
use App\Models\Bookmark;
use App\Models\Business;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookmarkBusinessTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        Artisan::call('passport:install', ['-vvv' => true]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBookmarkBusinessToggle()
    {
        $user        = factory(User::class)->create();
        $business    = factory(Business::class)->create();
        $credentials = ['email' => $user->email, 'password' => 'secret'];

        /**
         * Login
         */
        $response = $this->json('POST', '/api/v1/login', $credentials);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);

        /**
         * Bookmark Toggle
         */
        $token = $response->json('access_token');

        $response = $this->json('POST', '/api/v1/bookmark', [
            'uuid' => $business->uuid
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'business_id' => $business->id
        ]);
    }

    public function testBookmarkBusinessList()
    {
        /**
         * Setting Database
         */
        $user        = factory(User::class)->create();
        $business    = factory(Business::class)->create();
        $bookmark = Bookmark::create([
            'user_id' => $user->id,
            'business_id' => $business->id
        ]);

        $credentials = ['email' => $user->email, 'password' => 'secret'];

        /**
         * Login
         */
        $response = $this->json('POST', '/api/v1/login', $credentials);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);

        /**
         * Bookmark Listing
         */
        $token = $response->json('access_token');

        $response = $this->json('GET', '/api/v1/bookmark', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
            ->assertJson([
                [
                    'uuid'         => $business->uuid,
                    'name'         => $business->name,
                    'cover_photo'  => $business->cover_photo,
                    'score'        => $business->score
                ]
            ]);
    }
}

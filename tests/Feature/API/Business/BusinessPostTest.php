<?php

namespace Tests\Feature;

use App\Models\BusinessPost;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Carbon\Carbon;

class BusinessPostTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItStoresBusinessPost()
    {
        $user = factory(\App\Models\User::class)->create();
        Passport::actingAs($user);
        $business = factory(\App\Models\Business::class)->create([
            'user_id' => $user->id
        ]);

        $params       = [
            'text'        => 'aa',
            'business_id' => $business->uuid,
            'expire_date' => '2018-12-12',
            'photo' => UploadedFile::fake()->image('avatar.jpg')
        ];

        $response = $this->post( '/api/v1/business-posts', $params);
        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'business_id' => $business->id
                ]
            ]);

        $this->assertDatabaseHas('business_posts', [
            'user_id'     => $user->id
        ]);

        $image = $response->json('data.images');
        Storage::disk('public_new')->assertExists(last($image)['path']);
    }

    public function testUpdateBusinessPost()
    {
        $user = factory(\App\Models\User::class)->create();
        Passport::actingAs($user);
        $business = factory(\App\Models\Business::class)->create([
            'user_id' => $user->id
        ]);
        $businessPost = factory(\App\Models\BusinessPost::class)->create([
            'user_id' => $user->id,
            'uuid' => $business->uuid,
            'business_id' => $business->id,
        ]);


        $params = [
            'id'            =>  $businessPost->uuid,
            'text'          =>  'new business post text',
            'business_id'   =>  $businessPost->business->uuid,
            'expire_date'   =>  date("Y-m-d"),
            '_method'       => 'PUT'
        ];

        $response = $this->post('/api/v1/business-posts/'.$businessPost->uuid, $params);
        $response->assertStatus(200);

        $this->assertDatabaseHas('business_posts', [
            'business_id' => $businessPost->business->id,
            'text' => 'new business post text',
            'user_id'     => $user->id
        ]);

    }

    public function testActiveIndex()
    {
        $user = factory(\App\Models\User::class)->make();

        Passport::actingAs($user);

        $activeBusinessPost = factory(\App\Models\BusinessPost::class)->create();
        $params             = [
            'business_id' => $activeBusinessPost->business->uuid
        ];

        $response = $this->json('GET', '/api/v1/active-business-posts', $params);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function testItShowsPostsForSingleBusiness()
    {
        $user = factory(\App\Models\User::class)->make();

        Passport::actingAs($user);

        $activeBusinessPost = factory(\App\Models\BusinessPost::class)->create();
        $params             = [
            'business_id' => $activeBusinessPost->business->uuid
        ];

        $response = $this->get('/api/v1/business-posts/business/'.$params['business_id']);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'business_id',
                        'user_id',
                        'expire_date',
                        'text',
                        'meta'
                    ]
                ]
            ]);
    }

    public function testShowsSingleBusinessPost()
    {
        $user = factory(\App\Models\User::class)->make();

        Passport::actingAs($user);

        $activeBusinessPost = factory(\App\Models\BusinessPost::class)->create();
        $response           = $this->get("/api/v1/business-posts/{$activeBusinessPost->uuid}");
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'business_id',
                    'user_id',
                    'expire_date',
                    'text',
                    'meta'
                ]
            ]);
    }

    public function testBusinessPostDelete()
    {
        $user = factory(\App\Models\User::class)->create();

        Passport::actingAs($user);
        $activeBusinessPost = factory(\App\Models\BusinessPost::class, 5)->create([
            'user_id' => $user->id
        ]);
        $response           = $this->delete("/api/v1/business-posts/". $activeBusinessPost[0]->uuid);
        $response = json_decode($response->getContent(), true)['data']['success'];
        $this->assertTrue($response);
        $this->assertCount(4, BusinessPost::all());
    }

}


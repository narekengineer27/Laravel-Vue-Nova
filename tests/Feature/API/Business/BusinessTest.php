<?php

namespace Tests\Feature\API\Business;

use App\Elastic\Rules\AggregationRule;
use App\Models\Business;
use App\Models\MapPreset;
use Elasticsearch\ClientBuilder;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class BusinessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Create business
     *
     * @return void
     */
    public function testBusinessStoreSuccess()
    {
        $user     = factory(\App\Models\User::class)->create();
        $business = factory(\App\Models\Business::class)->make();
        $category = factory(\App\Models\Category::class)->create();
        $params   = [
            'name'        => $business->name,
            'lat'         => $business->lat,
            'lng'         => $business->lng,
            'category_id' => $category->uuid
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/api/v1/businesses', $params);
        $response->assertJsonStructure([
            'data' => [
                'name',
                'lat',
                'lng',
                'uuid'
            ]
        ]);
        $this->assertDatabaseHas('businesses', [
            'name' => $business->name
        ]);
    }

    /**
     * Create business
     *
     * @return void
     */
    public function testStoreSuccessWithSuppliedAvatarPhoto()
    {
        $user     = factory(\App\Models\User::class)->create();
        $business = factory(\App\Models\Business::class)->make();
        $category = factory(\App\Models\Category::class)->create();
        $file     = UploadedFile::fake()->image('avatar.png');
        $params   = [
            'name'        => $business->name,
            'lat'         => $business->lat,
            'lng'         => $business->lng,
            'category_id' => $category->uuid,
            'avatar'      => $file
        ];

        Passport::actingAs($user);
        Storage::fake('public');
        $response = $this->json('POST', '/api/v1/businesses', $params);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'lat',
                'lng',
                'uuid'
            ]
        ]);
        $this->assertDatabaseHas('businesses', [
            'name' => $business->name
        ]);
        $business = Business::where('name', $business->name)->firstOrFail();
        $this->assertNotNull($business->avatar);
    }

    public function testWeCanUpdateBusiness()
    {
        $user     = create('App\Models\User');
        $business = factory(\App\Models\Business::class)->make();
        $category = factory(\App\Models\Category::class)->create();
        $params   = [
            'name'        => $business->name,
            'lat'         => $business->lat,
            'lng'         => $business->lng,
            'category_id' => $category->uuid,
        ];

        Passport::actingAs($user);
        $response = $this->json('POST', '/api/v1/businesses', $params);
        $business = json_decode($response->getContent())->data;
        $uri = '/api/v1/businesses/' . $business->uuid;

        $params = [
            'name'        => 'Updated',
            'lat'         => $business->lat,
            'lng'         => $business->lng,
            'category_id' => $category->uuid,
        ];
        $response = $this->actingAs($user, 'api')
                        ->json('POST', $uri, $params)
                        ->assertStatus(200);


        $this->assertDatabaseHas('businesses', [
            'uuid' => $business->uuid,
            'name' => 'Updated',
        ]);
    }

    public function testDeleteBusiness()
    {
        $user     = factory(\App\Models\User::class)->create();
        $business = factory(\App\Models\Business::class)->make();
        $category = factory(\App\Models\Category::class)->create();

        Passport::actingAs($user);
        // Storage::fake('public');

        $params   = [
            'name' => $business->name,
            'lat' => $business->lat,
            'lng' => $business->lng,
            'category_id' => $category->getKey()
            // 'avatar' => $file = UploadedFile::fake()->image('avatar.png')
        ];

        // $business->avatar = $file->hashName();

        $response = $this->json('POST', '/api/v1/businesses', $params);
        // $business->uuid = $response->getData()->id;
        $response->assertStatus(201);

        $uuid = $response->getData()->id;

        $params = [
            'id' => $uuid,
        ];

        $response = $this->json('DELETE', '/api/v1/businesses', $params);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('businesses', [
            'uuid' => $uuid
        ]);
    }

    /**
     * Forbidden to create
     */
    public function testStoreForbidden()
    {
        $business = factory(\App\Models\Business::class)->make();
        $params   = [
            'name' => $business->name,
            'lat'  => $business->lat,
            'lng'  => $business->lng
        ];
        $this->expectException(AuthenticationException::class);
        $response = $this->json('POST', '/api/v1/businesses', $params);
        $response
            ->assertStatus(401);
    }

    /**
     * Forbidden to create with invalid data
     */
    public function testStoreBadData()
    {
        $user     = factory(\App\Models\User::class)->create();
        $business = factory(\App\Models\Business::class)->make();
        $category = factory(\App\Models\Category::class)->create();

        Passport::actingAs($user);

        $params   = [
            'name' => $business->name,
            'lat'  => $business->lat,
            'lng'  => $business->lng
        ];

        $response = $this->json('POST', '/api/v1/businesses', $params);
        $response
            ->assertStatus(400);
    }

    /**
     * Fetching business by ID
     */
    public function testShow()
    {
        $user     = factory(\App\Models\User::class)->make();
        $business = factory(\App\Models\Business::class)->create();

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/v1/businesses/{$business->uuid}");
        $response
            ->assertStatus(200)
            ->assertJson([
                 'id'   => $business->uuid,
                 'name' => $business->name
            ]);
    }

    public function testGetStats()
    {
        $user = factory(\App\Models\User::class)->make();

        Passport::actingAs($user);
        Storage::fake('s3:images');

        $hosts = [
            env('SCOUT_ELASTIC_HOST', 'localhost:9000')
        ];

        $topLeft['lat']     = 52.71;
        $topLeft['lng']     = -2.27;
        $bottomRight['lat'] = 51.02;
        $bottomRight['lng'] = 3.79;

        $elasticClient = ClientBuilder::create()->setHosts($hosts)->build();
        $search        = $elasticClient->search(AggregationRule::buildRule($topLeft, $bottomRight));
        $search        = $search['aggregations'];
        $params        = [
            'top_left'     => $topLeft,
            'bottom_right' => $bottomRight
        ];

        $response = $this->json('GET', '/api/v1/businesses/stats', $params);
        $response
            ->assertStatus(200)
            ->assertJson([
                'totalBusinesses' => $search['total_businesses']['value'],
                'totalImages'     => $search['total_images']['value'],
                'totalReviews'    => $search['total_reviews']['value']
            ]);
    }

    public function testGeoJson()
    {
        $response = $this->json('GET', '/api/v1/businesses/geo-json');
        $response
            ->assertStatus(200);

        $fileToDownload = last(explode("/", config('filesystems.geojson_path')));
        Storage::disk('local')->assertExists($fileToDownload);
    }

    public function testBusinessWithInActiveMapPreset()
    {
        $mapPresetHour = factory(\App\Models\MapPresetHours::class)->create([
            'open_period_mins'  => date('H:i', strtotime('-30 minutes')),
            'close_period_mins' => date('H:i', strtotime('+30 minutes')),
            'repeat'            => 'weekly'
        ]);

        $mapPreset = MapPreset::find($mapPresetHour->id);
        $mapPreset->isOpened = 0;
        $mapPreset->save();

        $user      = factory(\App\Models\User::class)->make();
        $params    = [
            'map_preset_id' => $mapPreset->uuid
        ];

        Passport::actingAs($user);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Map preset is inactive currently');
        $response = $this->json('GET', "/api/v1/businesses", $params);
    }

    public function testBusinessWithActiveMapPreset()
    {
        $mapPresetHour = factory(\App\Models\MapPresetHours::class)->create([
            'wd_' . date('w')   => true,
            'open_period_mins'  => date('H:i', strtotime('-30 minutes')),
            'close_period_mins' => date('H:i', strtotime('+30 minutes')),
            'repeat'            => 'weekly'
        ]);

        $mapPreset = MapPreset::find($mapPresetHour->id);
        $mapPreset->isOpened = 1;
        $mapPreset->save();

        $user      = factory(\App\Models\User::class)->make();
        $params    = [
            'map_preset_id' => $mapPreset->uuid
        ];

        Passport::actingAs($user);

        $response = $this->json('GET', "/api/v1/businesses", $params);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => []
            ]);

    }
}

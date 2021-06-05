<?php

namespace Tests\Feature\API\Category;

use Tests\TestCase;
// use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class CategoryTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }


    public function testCategoryIconUploaded()
    {
        $user     = factory(\App\Models\User::class)->create();
        $category = factory(\App\Models\Category::class)->make();
        $category->name = substr($category->name, 0, 10);

        Passport::actingAs($user);
        Storage::fake('public');

        $params   = [
            'name' => $category->name,
            'icon' => $file = UploadedFile::fake()->image('test.png')
        ];

        $response = $this->json('POST', '/api/v1/categories', $params);
        $response->assertStatus(201);
        $response = \GuzzleHttp\json_decode(($response->getContent()));
        $category->uuid = $response->id;
        $category->icon = $response->icon;

        // Assert the file was stored...
        Storage::disk('public')->assertExists($category->icon);

        $this->assertDatabaseHas('categories', [
            'uuid' => $category->uuid,
            'name' => $category->name,
            'icon' => $category->icon,
        ]);

    }

    public function testUpdateCategory()
    {
        $user     = factory(\App\Models\User::class)->create();
        $category = factory(\App\Models\Category::class)->make();
        $category->name = substr($category->name, 0, 10);

        Passport::actingAs($user);
        Storage::fake('public');

        $params   = [
            'name' => $category->name,
            'icon' => $file = UploadedFile::fake()->image('test.png')
        ];

        $category->icon = $file->hashName();

        $response = $this->json('POST', '/api/v1/categories', $params);
        $response->assertStatus(201);
        $response = \GuzzleHttp\json_decode(($response->getContent()));
        $category->uuid = $response->id;


        $params = [
            'id' => $category->uuid,
            'name' => $category->name,
            'icon' => $category->icon
        ];

        $response = $this->json('PUT', '/api/v1/categories', $params);

        $response->assertStatus(200);

        $this->assertDatabaseHas('categories', [
            'uuid' => $category->uuid,
            'name' => $category->name,
            'icon' => $category->icon,
        ]);

    }

    public function testDeleteCategory()
    {
        $user     = factory(\App\Models\User::class)->create();
        $category = factory(\App\Models\Category::class)->make();
        $category->name = substr($category->name, 0, 10);

        Passport::actingAs($user);
        Storage::fake('public');

        $params   = [
            'name' => $category->name,
            'icon' => $file = UploadedFile::fake()->image('test.png')
        ];

        $category->icon = $file->hashName();

        $response = $this->json('POST', '/api/v1/categories', $params);
        $response->assertStatus(201);

        $response = \GuzzleHttp\json_decode(($response->getContent()));
        $category->uuid = $response->id;


        $params = [
            'id' => $category->uuid,
        ];

        $response = $this->json('DELETE', '/api/v1/categories', $params);
        $response->assertStatus(200);
        Storage::disk('public')->assertMissing('icons'.$file->hashName());

        $this->assertDatabaseMissing('categories', [
            'uuid' => $category->uuid
        ]);

    }
}

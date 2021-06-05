<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/01/19
 * Time: 2:02 PM
 */

namespace Tests\Unit\Controllers;


use App\Http\Controllers\API\v1\BusinessesController;
use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class BusinessesControllerTest extends TestCase
{
    /**
     * Check that invalid data gets caught
     *
     */
    public function testStoreBusinessBadData()
    {
        $user = create('App\Models\User');

        $business = make('App\Models\Business', [
           'name' => null,
           'lat' => null,
           'lng' => null
        ]);

        $this->withExceptionHandling()
             ->actingAs($user, 'api')
             ->postJson('api/v1/businesses', $business->toArray())
             ->assertStatus(422)
             ->assertJsonStructure([ 'data' => [
                 'errors'
             ]
            ])->assertJsonFragment(
                [
                    'name' => [
                    'The name field is required.'
                    ],
                    'lat' => [
                        'The lat field is required.'
                    ],
                    'lng' => [
                        'The lng field is required.'
                    ]
                ]
            );
    }


    /**
     * Check that valid data results in a business owned by the creating user
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return void
     */
    public function testStoreBusinessValidData()
    {
        $category     = factory(Category::class)->create();
        $user         = factory(User::class)->create();

        $initBusiness = Business::count();
        $this->assertEquals(0, $user->businesses()->count());

        $business = make(Business::class, [
            'lat' => -27.47,
            'lng' => 153.02,
            'category_id' => $category->getKey(),
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson('api/v1/businesses', $business->toArray());


        // check consequences of successful store - first, that we have another business
        $finalBusiness = Business::count();
        $this->assertEquals($initBusiness + 1, $finalBusiness);

        // second, check that the new business belongs to the user who created it
        $user = User::findOrFail($user->getKey());
        $this->assertEquals(1, $user->businesses()->count());

        // third, check that the new business has what we gave it
        $business = $user->businesses()->first();

        $this->assertDatabaseHas('business_category',
            $business->categories()->first()->pivot->toArray()
        );

        // specifically check the category relation as it's a many to many relation
        $nuCategory = $business->categories()->first();
        $this->assertTrue($nuCategory instanceof Category);
        $this->assertEquals($category->getKey(), $nuCategory->getKey());
        $nuBusiness = $category->businesses()->first();
        $this->assertTrue($nuBusiness instanceof Business);
        $this->assertEquals($business->getKey(), $nuBusiness->getKey());
    }

    /**
     * Check that non-text business bio doesn't result in a new business
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return void
     */
    public function testStoreBusinessValidMandatoryDataButInvalidBio()
    {
        $category     = factory(Category::class)->create();

        $user         = create('App\Models\User');

        $business = make('App\Models\Business', [
            'lat' => -27.47,
            'lng' => 153.02,
            'category_id' => $category->getKey(),
            'bio' => '',
        ]);

        $this->assertEquals(0, $user->businesses()->count());


        $response = $this->withExceptionHandling()
            ->actingAs($user, 'api')
            ->postJson('api/v1/businesses', $business->toArray())
            ->assertJsonFragment([
                'message' => [
                    'The bio must be a string.'
                    ]
            ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Check that valid data with text bio results in a business owned by the creating user
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return void
     */
    public function testStoreBusinessValidMandatoryDataAndValidBio()
    {
        $category     = factory(Category::class)->create();
        $user         = factory(User::class)->create();

        $initBusiness = Business::count();
        $this->assertEquals(0, $user->businesses()->count());

        $business = [
            'name' => 'Business Name',
            'lat' => -27.47,
            'lng' => 153.02,
            'category_id' => $category->getKey(),
            'bio' => 'Business bio'
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson('api/v1/businesses', $business);

        // check consequences of successful store - first, that we have another business
        $finalBusiness = Business::count();
        $this->assertEquals($initBusiness + 1, $finalBusiness);

        // second, check that the new business belongs to the user who created it
        $user = User::findOrFail($user->getKey());
        $this->assertEquals(1, $user->businesses()->count());

        $business = $user->businesses()->first();

    }
}

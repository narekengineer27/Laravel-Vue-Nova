<?php

namespace Tests\Unit\Business;

use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BusinessTest extends TestCase
{
    /**
     * Test 0 internal score
     *
     * @return void
     */
    public function testZeroInternalScore()
    {
        $business = factory(Business::class)->create();
        $business = Business::find($business->id);

        $this->assertEquals(0, $business->internal_score);
    }

    /**
     * Test business with review only
     * Score must be 20
     */
    public function testReviewInternalScore()
    {
        $business       = factory(Business::class)->create();
        $businessReview = factory(\App\Models\BusinessReview::class)->create([
             'business_id' => $business->id
        ]);

        $business = Business::find($business->id);
        $this->assertEquals(20, $business->internal_score);
    }

    /**
     * Test business with categories only
     * Score must be 20
     */
    public function testCategoriesInternalScore()
    {
        $business         = factory(Business::class)->create();
        $businessCategory = factory(\App\Models\BusinessCategory::class)->create([
            'business_id' => $business->id
        ]);

        $business = Business::find($business->id);
        $this->assertEquals(20, $business->internal_score);
    }

    /**
     * Test business with addy attribute only
     * Score must be 20
     */
    public function testAddyAttributesInternalScore()
    {
        $business          = factory(Business::class)->create();
        $businessAttribute = factory(\App\Models\BusinessAttribute::class)->create([
             'business_id' => $business->id,
             'key'         => 'addy'
        ]);

        $business = Business::find($business->id);
        $this->assertEquals(20, $business->internal_score);
    }

    /**
     * Test business with attributes count > 2
     * Score must be 20
     */
    public function testAttributesInternalScore()
    {
        $business         = factory(Business::class)->create();
        $businessCategory = factory(\App\Models\BusinessAttribute::class, 2)->create([
            'business_id' => $business->id,
        ]);

        $business = Business::find($business->id);
        $this->assertEquals(20, $business->internal_score);
    }

    /**
     * Score must be 100
     */
    public function testInternalScore()
    {
        $business = factory(Business::class)->create();
        factory(\App\Models\BusinessReview::class)->create([
            'business_id' => $business->id
        ]);
        factory(\App\Models\BusinessPost::class)->create([
            'business_id' => $business->id
        ]);
        factory(\App\Models\BusinessCategory::class)->create([
            'business_id' => $business->id
        ]);
        factory(\App\Models\BusinessAttribute::class)->create([
            'business_id' => $business->id,
            'key'         => 'addy'
        ]);
        factory(\App\Models\BusinessAttribute::class, 2)->create([
            'business_id' => $business->id,
        ]);

        $business = Business::find($business->id);
        $this->assertEquals(100, $business->internal_score);
    }

    /**
     * Test business basic score
     * Must be 80 - minimum
     */
    public function testScore()
    {
        $business = factory(Business::class)->create();
        $business->updateScores();
        $business = Business::find($business->id);

        $this->assertEquals(80, $business->score);
    }

    /**
     * Score must be 100
     */
    public function testGraterScore()
    {
        $business       = factory(Business::class)->create();
        $businessReview = factory(\App\Models\BusinessReview::class, 7)->create([
            'business_id' => $business->id,
            'score'        => 5
        ]);

        $business = Business::find($business->id);
        $this->assertEquals(100, $business->score);
    }

    /**
     * Verify that user->businesses and business->user relations round trip
     */
    public function testOwnedBusinessRelationRoundTrip()
    {
        $user           = factory(\App\Models\User::class)->create();
        $business       = factory(Business::class)->create(['user_id' => $user->getKey()]);

        $nuUser = $business->user()->first();
        $this->assertTrue($nuUser instanceof User);
        $this->assertEquals($user->getKey(), $nuUser->getKey());
        $nuBusiness = $user->businesses()->first();
        $this->assertTrue($nuBusiness instanceof Business);
        $this->assertEquals($business->getKey(), $nuBusiness->getKey());
    }

    public function testAvatarPhotoUrlAttributeEmptyAvatar()
    {
        $user           = factory(\App\Models\User::class)->create();
        $business       = factory(Business::class)->create(['user_id' => $user->getKey()]);

        $avatar = $business->avatar;
        $this->assertNull($avatar);
    }

    public function testAvatarPhotoUrlAttributeNonEmptyAvatarButNoFile()
    {
        File::shouldReceive('exists')->andReturn(false);
        $user           = factory(\App\Models\User::class)->create();
        $business       = factory(Business::class)->create(['user_id' => $user->getKey(), 'avatar' => 'avatar']);

        $avatar = $business->avatar;
        $this->assertNull($avatar);
    }

    public function testAvatarPhotoUrlAttributeNonEmptyAvatarButHasFile()
    {
        File::shouldReceive('exists')->andReturn(true);
        $avatarFile     = public_path('storage/avatar.jpg');
        $expectedUrl    = url('storage/avatar.jpg');

        $user           = factory(\App\Models\User::class)->create();
        $business       = factory(Business::class)->create(['user_id' => $user->getKey(), 'avatar' => $avatarFile]);

        $avatar = $business->avatar;
        $this->assertEquals($expectedUrl, $avatar);
    }

    /**
     * @test
     *
     * @dataProvider boundsProvider
     *
     */
    public function latitudes_in_bounds($west, $longitude, $east, $result)
    {
        $north = 10;
        $south = -10;
        $coordinates = [$longitude, $latitude = 0];

        $this->assertEquals($result, \HelperServiceProvider::inBounds($coordinates, $west, $south, $east, $north));
    }

    /**
     * @test
     *
     * @dataProvider boundsProvider
     *
     */
    public function longitudes_in_bounds($south, $latitude, $north, $result)
    {
        $east = 10;
        $west = -10;
        $coordinates = [$longitude = 0, $latitude];
        
        $this->assertEquals($result, \HelperServiceProvider::inBounds($coordinates, $west, $south, $east, $north));
    }

    public function boundsProvider()
    {
        return [
            [-10, 0, 10, true],
            [-30, -20, -10, true],
            [10, 20, 30, true],
            [-10, -20, 10, false],
            [-10, 20, 10, false],
            [-30, -40, -10, false],
            [-30, 0, -10, false],
            [10, 0, 30, false],
            [10, 40, 30, false],
        ];
    }
}

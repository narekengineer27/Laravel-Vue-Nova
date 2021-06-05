<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/01/19
 * Time: 10:50 PM
 */

namespace Tests\Unit\Repositories;

use App\Elastic\Entities\Feed;
use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\BusinessPostImage;
use App\Repositories\FeedRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FeedRepositoryTest extends TestCase
{
    /**
     * Get all the businesses a newly-created user owns - ie, none
     */
    public function testGetOwnedBusinessesWhenNone()
    {
        $user   = factory(\App\Models\User::class)->create();
        Passport::actingAs($user);

        $foo = new FeedRepository(new \App\Elastic\Entities\Feed, new \App\Models\User());

        $actual = $foo->userOwnedBusinesses();
        $this->assertEquals(10, $actual->perPage());
        $this->assertEquals(0, $actual->total());
        $this->assertEquals(1, $actual->currentPage());
        $this->assertFalse($actual->hasMorePages());
    }

    /**
     * Get all the businesses a newly-created user owns - ie, none
     */
    public function testGetOwnedBusinessesSinglePage()
    {
        $user       = factory(\App\Models\User::class)->create();
        $businesses = factory(Business::class, 9)->create(['user_id' => $user->getKey()]);
        Passport::actingAs($user);

        $foo = new FeedRepository(new \App\Elastic\Entities\Feed, new \App\Models\User());

        $actual = $foo->userOwnedBusinesses();
        $this->assertEquals(10, $actual->perPage());
        $this->assertEquals(9, $actual->total());
        $this->assertEquals(1, $actual->currentPage());
        $this->assertFalse($actual->hasMorePages());
    }

    /**
     * Get all the businesses a newly-created user owns - ie, none
     */
    public function testGetOwnedBusinessesMultiplePage()
    {
        $user       = factory(\App\Models\User::class)->create();
        $businesses = factory(Business::class, 42)->create(['user_id' => $user->getKey()]);
        Passport::actingAs($user);

        $foo = new FeedRepository(new \App\Elastic\Entities\Feed, new \App\Models\User());

        $actual = $foo->userOwnedBusinesses();
        $this->assertEquals(10, $actual->perPage());
        $this->assertEquals(10, $actual->total());
        $this->assertEquals(1, $actual->currentPage());
        $this->assertFalse($actual->hasMorePages());
    }
   public function testGetFirstPageOfHomeFeedEnoughBusinesses()
    {
        $businesses = factory(Business::class, 4)->create(['lat' => -27.41, 'lng' => 153.02]);

        foreach ($businesses as $bus) {
            $posts = factory(BusinessPost::class, 9)->create(['business_id' => $bus->getKey()]);
            foreach ($posts as $post) {
                factory(BusinessPostImage::class, rand(0, 3))->create(['business_post_id' => $post->getKey()]);
            }
        }

        $businessId = Business::search("*")
            ->whereGeoDistance("location", [153.02, -27.41], "100m")->orderBy('score', 'desc')->get()->pluck('id')->toArray();

        $feed = new Feed();

        $foo = new FeedRepository($feed, new \App\Models\User());

        $result = $foo->forHomeFeed(-27.41, 153.02, '100m');
        $this->assertTrue($result instanceof LengthAwarePaginator);

        $expectedKeys = ['id', 'business_id', 'user_id', 'expire_date', 'text', 'meta', 'deleted_at', 'created_at',
            'updated_at', 'score', 'business_name', 'business_uuid', 'user_avatar', 'user_name', 'images', 'photo_url'];

        $scratch = $result->getCollection();
        $data = $scratch['data'];
        $this->assertEquals(10, count($data));
        $actualKeys = array_keys($data[0]);
        $this->assertEquals($expectedKeys, $actualKeys);
    }
}

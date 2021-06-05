<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 23/01/19
 * Time: 4:03 PM
 */

namespace Tests\Feature\API\User;


use App\Http\Controllers\API\v1\UserFeedController;
use App\Http\Resources\UserFeedResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserHomeFeedTest extends TestCase
{
    /**
     *
     */
    public function testGetEmptyHomeFeed()
    {
        $user   = factory(User::class)->create();
        Passport::actingAs($user);

        $paginator = new LengthAwarePaginator([], 0, 12);
        $result = new AnonymousResourceCollection($paginator, UserFeedResource::class);

        // Since we've already tested the call path from the controller down to the service and back,
        // we'll mock the controller return call directly.  The ->once() decorator is an assertion that, by the end of
        // the test, it's been called exactly once - never is not enough and twice is right out.
        $foo = \Mockery::mock(UserFeedController::class)->makePartial();
        $foo->shouldReceive('homeFeed')->andReturn($result)->once();

        // now we'll lift up the framework, slide the mock controller underneath in place of the original controller,
        // and drop the framework back down
        App::instance(UserFeedController::class, $foo);

        // now make the HTTP call
        $url = '/api/v1/user-home-feed';
        $response = $this->json('GET', $url);
        $response->assertStatus(200);
        // check content
        $response->assertJsonFragment(['total' => 0, 'per_page' => 12, 'current_page' => 1, 'last_page' => 1]);
    }
}

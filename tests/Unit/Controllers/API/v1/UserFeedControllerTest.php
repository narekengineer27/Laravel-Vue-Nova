<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 23/01/19
 * Time: 3:37 PM
 */

namespace Tests\Unit\Controllers\API\v1;


use App\Http\Controllers\API\v1\UserFeedController;
use App\Http\Resources\UserFeedResource;
use App\Repositories\FeedRepository;
use App\Services\FeedService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class UserFeedControllerTest extends TestCase
{

    /**
     * Test empty user feed retrieval
     *
     * @throws \Exception
     * @return void
     */
    public function testIndexEmptyRetrieval()
    {
        $paginator = new LengthAwarePaginator([], 0, 12);

        $repo = \Mockery::mock(FeedRepository::class);
        $repo->shouldReceive('forUser')->andReturn($paginator)->once();
        $service = new FeedService($repo);

        // now hit up the controller method and check what we get back
        // need to call controller something, so might as well call it $foo
        $foo = new UserFeedController();

        $result = $foo->index($service);
        $this->assertTrue($result instanceof AnonymousResourceCollection, get_class($result));
    }

    /**
     * Test empty user home feed retrieval
     *
     * @throws \Exception
     * @return void
     */
    public function testHomeFeedEmptyRetrieval()
    {
        $paginator = new LengthAwarePaginator([], 0, 12);

        $repo = \Mockery::mock(FeedRepository::class);
        $repo->shouldReceive('forHomeFeed')->andReturn($paginator)->once();
        $service = new FeedService($repo);

        // now hit up the controller method and check what we get back
        // need to call controller something, so might as well call it $foo
        $foo = new UserFeedController();

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('input')->withArgs(['lat'])->andReturn(42);
        $request->shouldReceive('input')->withArgs(['lng'])->andReturn(11);
        $request->shouldReceive('input')->withArgs(['distance'])->andReturn('1500m');

        $result = $foo->homeFeed($service, $request);
        $this->assertTrue($result instanceof AnonymousResourceCollection, get_class($result));
    }
}

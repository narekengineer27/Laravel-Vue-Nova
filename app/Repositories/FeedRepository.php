<?php
/**
 * Created by PhpStorm.
 * User: byabuzyak
 * Date: 11/21/18
 * Time: 12:46 PM
 */

namespace App\Repositories;

use App\Elastic\Entities\Feed;
use App\Elastic\Rules\FeedRule;
use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FeedRepository
{
    /**
     * @var Feed
     */
    protected $model;

    /**
     * @var User
     */
    protected $userModel;

    /**
     * @var int
     */
    public $size = 10;

    /**
     * FeedRepository constructor.
     * @param Feed $feed
     * @param User $userModel
     */
    public function __construct(Feed $feed, User $userModel)
    {
        $this->model = $feed;
        $this->userModel = $userModel;
    }

    /**
     * @param $lat
     * @param $lng
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function get($lat, $lng) {
        return
            $this
                ->model
                ->rule(FeedRule::build($lat, $lng))
                ->setHitSource('top')
                ->paginate();
    }

    /**
     * @param $businessId
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function forBusiness($businessId)
    {
        return
            $this
                ->model
                ->query(FeedRule::business($businessId))
                ->paginate()
            ;
    }

    /**
     * @return mixed
     */
    public function forUser()
    {
        $user       = Auth::user();
        $reviews    = $user->reviews()->paginate($this->size);
        $businesses = $user->businesses()->paginate($this->size);
        $results    = $reviews->merge($businesses)->shuffle()->makeHidden(['user_id']);

        return
            new LengthAwarePaginator(
                $results,
                $results->count(),
                $this->size,
                Paginator::resolveCurrentPage('page'),
                [
                    'path'     => Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );
    }
    /**
     * @return mixed
     */
    public function userOwnedBusinesses()
    {
        $user       = Auth::user();
        $results    = $user->businesses()->paginate($this->size);

        return
            new LengthAwarePaginator(
                $results,
                $results->count(),
                $this->size,
                Paginator::resolveCurrentPage('page'),
                [
                    'path'     => Paginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );
    }

    /**
     * @param float|null $lat
     * @param float|null $lng
     * @param string|null $distance
     * @param int $iter
     * @return mixed
     */
    public function forHomeFeed(?float $lat, ?float $lng, ?string $distance, $iter = 0)
    {
        $businessId = Business::search("*")
            ->whereGeoDistance("location", [$lng, $lat], $distance."km")
            ->orderBy('score', 'desc')
            ->take(10000)
            ->get()->pluck('id')->toArray();

        if(count($businessId) < 100 && $distance < (20 * 1000)) { //40K km is circumference of earth
            $distance *= 2;
            return $this->forHomeFeed($lat, $lng, $distance, ++$iter);
        }
        $results = BusinessPost::whereIn('business_id', $businessId)->with('user', 'business')
            ->inRandomOrder(1788)->paginate(10);

        return $results;
    }
}

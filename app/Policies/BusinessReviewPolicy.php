<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Business;
use App\Models\BusinessReview;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class BusinessReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the business review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\BusinessReview  $businessReview
     * @return mixed
     */
    public function view(User $user, BusinessReview $businessReview)
    {
        //
    }

    /**
     * Determine whether the user can create business reviews.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $businessUuid = str_replace("business-reviews/","", strstr(request()->url(), 'business-reviews/', false));
        return (bool) !in_array($businessUuid, $user->businesses()->pluck('uuid')->toArray());
    }

    /**
     * Determine whether the user can update the business review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\BusinessReview  $businessReview
     * @return mixed
     */
    public function update(User $user, BusinessReview $businessReview)
    {
        //
    }

    /**
     * Determine whether the user can delete the business review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\BusinessReview  $businessReview
     * @return mixed
     */
    public function delete(User $user, BusinessReview $businessReview)
    {
        //
    }

    /**
     * Determine whether the user can restore the business review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\BusinessReview  $businessReview
     * @return mixed
     */
    public function restore(User $user, BusinessReview $businessReview)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the business review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\BusinessReview  $businessReview
     * @return mixed
     */
    public function forceDelete(User $user, BusinessReview $businessReview)
    {
        //
    }
}

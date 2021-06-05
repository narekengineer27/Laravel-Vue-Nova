<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Business;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessPolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        if($user->hasRole('admin')) {
            return true;
        };
    }

    /**
     * Determine whether the user can view the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function view(User $user, Business $business)
    {
        return true;
    }

    /**
     * Determine whether the user can create businesses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function update(User $user, Business $business)
    {
        return in_array($business->id, $user->businesses()->pluck('id')->toArray());
    }


    /**
     * Determine whether the user can delete the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function delete(User $user, Business $business)
    {
        return in_array($business->id, $user->businesses()->pluck('id')->toArray());
    }



    /**
     * Determine whether the user can restore the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function restore(User $user, Business $business)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the business.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Business  $business
     * @return mixed
     */
    public function forceDelete(User $user, Business $business)
    {
        //
    }
}

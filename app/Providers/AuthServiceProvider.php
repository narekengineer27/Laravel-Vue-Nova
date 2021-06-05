<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\BusinessReview;
use App\Policies\BusinessPolicy;
use App\Policies\BusinessReviewPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Business::class => BusinessPolicy::class,
        BusinessReview::class => BusinessReviewPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            if($user->hasRole('admin')) {
                return true;
            };
        });

        Passport::routes();
    }
}

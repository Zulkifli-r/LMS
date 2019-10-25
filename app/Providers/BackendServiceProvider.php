<?php

namespace App\Providers;

use App\Repositories\AccountRepository;
use App\Repositories\ClassroomInvitationRepository;
use Illuminate\Support\ServiceProvider;

class BackendServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AccountRepository::class, function ($app) {
            return new AccountRepository(auth('api')->user());
        });

        $this->app->bind(ClassroomInvitationRepository::class, function ($app) {
            return new ClassroomInvitationRepository(auth('api')->user());
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

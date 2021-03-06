<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Listeners\SendUserRegisteredEmail;
use App\Listeners\SubscribeUserToMailchimp;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserRegistered::class => [
            SendUserRegisteredEmail::class,
            // SubscribeUserToMailchimp::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

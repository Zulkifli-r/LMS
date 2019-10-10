<?php

namespace App\Listeners;

use App\Mail\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendUserRegisteredEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $isEmail = filter_var($event->user->email, FILTER_VALIDATE_EMAIL );
        if ($isEmail) {
            Mail::queue(new UserRegistered($event->user));
        }
    }
}

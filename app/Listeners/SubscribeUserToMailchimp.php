<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Newsletter\NewsletterFacade as Newsletter;

class SubscribeUserToMailchimp
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
            Newsletter::subscribeOrUpdate($event->user->email, ['FNAME' => $event->user->name]);
        }
    }
}

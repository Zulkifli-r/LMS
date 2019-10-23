<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClassroomInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invitation ;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invitation, $token)
    {
        $this->invitation = $invitation;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('support@gakken-idn.co.id', 'Gakken Indonesia')
                    ->subject('LMS Classroom Invitation')
                    ->view('emails.classroom.invitation')
                    ->with([
                        'invitation' => $this->invitation,
                        'token' => $this->token
                    ]);
    }
}

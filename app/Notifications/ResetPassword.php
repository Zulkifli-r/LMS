<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from('support@gakken-idn.co.id', 'Gakken Indonesia')
                    ->subject(Lang::get('Reset Password Notification'))
                    ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
                    ->line('Follow this link to reset your password.')
                    ->action('Notification Action', env('FE_RESET_PASSWORD_ROUTE','http://localhost:3000/reset/set-password').'?token='.$this->token.'&email='.$notifiable->getEmailForPasswordReset())
                    ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }
}

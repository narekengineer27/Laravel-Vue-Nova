<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    /**
     * @inherit
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable);
        }

        return (new MailMessage)
            ->subject(__('Verify Email Address'))
            ->line(__('Thanks for signing up!, please click the below to verify your email address.'))
            ->action(
                __('Verify Email Address'),
                $this->verificationUrl($notifiable)
            )
            ->line(__('If you did not create an account, no further action is required.'));
    }

    /**
     * @inherit
     */
    public function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.email',
            Carbon::now()->addMinutes(60),
            ['id' => $notifiable->getKey()]
        );
    }


    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class VerifySmsNotification extends Notification
{
    use Queueable;

    public $pin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pin)
    {
        //
        $this->pin = $pin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }
    
    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage)
            ->content("Thanks for registering to ".config('app.name').". Your verification pin is {$this->pin}. Please sign in to verify your account.");
    }
}

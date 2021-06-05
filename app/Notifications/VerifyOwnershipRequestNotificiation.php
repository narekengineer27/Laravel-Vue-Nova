<?php

namespace App\Notifications;

use App\Models\Business;
use App\Models\OwnershipRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyOwnershipRequestNotificiation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($pin, Business $business, OwnershipRequest $ownershipRequest)
    {
        $this->pin = $pin;
        $this->business = $business;
        $this->ownershipRequest = $ownershipRequest;
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
                    ->line('Your business ownership request has been submitted successfully.')
                    ->line("PIN: {$this->pin}")
                    ->line('Follow the link below to complete verification.')
                    ->action("Verify Business Ownership Of {$this->business->name}", route('business.verification', $this->ownershipRequest))
                    ->line('Thank you for using our service!');
    }

}

<?php

namespace Biigle\Modules\Ananas\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AnnotationAssistanceRequest extends Notification implements ShouldQueue
{
    use Queueable;

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
     * @param  mixed  $notifiable  The assistance request
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $name = $notifiable->user->firstname.' '.$notifiable->user->lastname;

        return (new MailMessage)
            ->replyTo($notifiable->user->email, $name)
            ->subject("Annotation Assistance Request from {$name}")
            ->greeting('Hello!')
            ->line("{$name} asks you for assistance with an annotation in BIIGLE.")
            ->action("Help {$notifiable->user->firstname}", route('respond-assistance-request', $notifiable->token));
    }
}

<?php

namespace Biigle\Modules\Ananas\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest as Ananas;

class AnnotationAssistanceRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The assistance request.
     *
     * @var Ananas
     */
    public $request;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ananas $request)
    {
        $this->request = $request;
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
        $name = $this->request->user->firstname.' '.$this->request->user->lastname;

        return (new MailMessage)
            ->greeting('Hello!')
            ->line("{$name} asks you for assistance with an annotation in BIIGLE.")
            ->action("Help {$name}", route('respond-assistance-request', $this->request->token));
    }
}

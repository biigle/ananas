<?php

namespace Biigle\Modules\Ananas\Notifications;

use Biigle\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest as Ananas;

class AnnotationAssistanceRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The annotation assistance request.
     *
     * @var Ananas
     */
    public $request;

    /**
     * Create a new instance.
     *
     * @param Ananas $request
     */
    public function __construct(Ananas $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  User  $notifiable
     * @return array
     */
    public function via(User $notifiable)
    {
        $settings = config('ananas.notifications.default_settings');

        if (config('ananas.notifications.allow_user_settings') === true) {
            $settings = $notifiable->getSettings('ananas_notifications', $settings);
        }

        if ($settings === 'web') {
            return ['database'];
        }

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User  $notifiable  The assistance request
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable)
    {
        $name = $this->request->user->firstname.' '.$this->request->user->lastname;

        return (new MailMessage)
            ->replyTo($this->request->user->email, $name)
            ->subject("Annotation Assistance Request from {$name}")
            ->greeting("Hello {$notifiable->firstname}!")
            ->line("{$name} asks you for assistance with an annotation in BIIGLE.")
            ->action("Help {$this->request->user->firstname}", route('respond-assistance-request', $this->request->token));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  User  $notifiable
     * @return array
     */
    public function toArray(User $notifiable)
    {
        $name = $this->request->user->firstname.' '.$this->request->user->lastname;

        return [
            'title' => "Annotation Assistance Request from {$name}",
            'message' => "{$name} asks you for assistance with an annotation.",
            'action' => "Help {$this->request->user->firstname}",
            'actionLink' => route('respond-assistance-request', $this->request->token),
        ];
    }
}

<?php

namespace Biigle\Modules\Ananas\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Biigle\Notifications\InAppNotification as Notification;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest as Ananas;

class AnnotationAssistanceResponse extends Notification implements ShouldQueue
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ananas $request)
    {
        parent::__construct(
            'Annotation Assistance Response',
            "{$request->email} responded to your annotation assistance request!",
            null,
            'View it here',
            route('show-assistance-request', $request->id)
        );
    }
}
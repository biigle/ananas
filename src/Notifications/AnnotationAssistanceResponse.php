<?php

namespace Biigle\Modules\Ananas\Notifications;

use Biigle\Modules\Ananas\AnnotationAssistanceRequest as Ananas;
use Biigle\Notifications\InAppNotification as Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

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
            'You got a response to your annotation assistance request!',
            null,
            'View it here',
            route('show-assistance-request', $request->id)
        );
    }
}

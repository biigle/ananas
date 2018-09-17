<?php

namespace Biigle\Modules\Ananas\Observers;

use Biigle\Modules\Ananas\AnnotationAssistanceRequest as Ananas;
use Biigle\Modules\Ananas\Notifications\AnnotationAssistanceRequest as AnanasNotification;

class AnnotationAssistanceRequestObserver
{
    /**
     * Handle the event of creatinga new assistance request.
     *
     * @param Ananas $request
     */
    public function created(Ananas $request)
    {
        if ($request->receiver_id !== null) {
            $request->receiver->notify(new AnanasNotification($request));
        }
    }
}

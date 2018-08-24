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
        $request->notify(new AnanasNotification);
    }
}

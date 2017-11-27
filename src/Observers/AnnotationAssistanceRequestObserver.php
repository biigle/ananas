<?php

namespace Biigle\Modules\Ananas\Observers;

use Biigle\User;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest as Ananas;
use Biigle\Modules\Ananas\Notifications\AnnotationAssistanceRequest as AnanasNotification;

class AnnotationAssistanceRequestObserver
{
    /**
     * Handle the event of creatinga new assistance request.
     *
     * @param Ananas $request
     */
    public function saved(Ananas $request)
    {
        // The receiver of this notification does not have to be an actual Biigle user.
        // We create (and don't save) a temporary user instead.
        $tmpUser = (new User)->forceFill(['email' => $request->email]);
        $tmpUser->notify(new AnanasNotification($request));
    }
}

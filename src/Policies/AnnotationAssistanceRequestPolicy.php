<?php

namespace Biigle\Modules\Ananas\Policies;

use Biigle\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;

class AnnotationAssistanceRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Intercept all checks.
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before($user, $ability)
    {
        if ($user->isAdmin) {
            return true;
        }
    }

    /**
     * Determine if the given assistance request can be deleted by the user.
     *
     * @param  User  $user
     * @param  AnnotationAssistanceRequest  $request
     * @return bool
     */
    public function destroy(User $user, AnnotationAssistanceRequest $request)
    {
        return $request->user_id === $user->id;
    }
}

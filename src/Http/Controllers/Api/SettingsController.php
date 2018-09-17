<?php

namespace Biigle\Modules\Ananas\Http\Controllers\Api;

use Biigle\User;
use Illuminate\Http\Request;
use Biigle\Http\Controllers\Api\Controller;

class SettingsController extends Controller
{
    /**
     * Update the user settings for annotation assistance requests.
     *
     * @api {post} users/my/settings/ananas Update the user settings for ananas
     * @apiGroup Users
     * @apiName StoreUsersAnanasSettings
     * @apiPermission user
     *
     * @apiParam (Optional arguments) {String} ananas_notifications Set to `'email'` or `'web'` to receive notifications for new annotation assistance requests either via email or in the BIIGLE notification center.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (config('ananas.notifications.allow_user_settings') === false) {
            abort(404);
        }
        $this->validate($request, [
            'ananas_notifications' => 'filled|in:email,web',
        ]);
        $settings = $request->only(['ananas_notifications']);
        $request->user()->setSettings($settings);
    }
}

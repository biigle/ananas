<?php

namespace Biigle\Modules\Ananas\Http\Controllers\Api;

use Biigle\Label;
use Carbon\Carbon;
use Biigle\Project;
use Biigle\Annotation;
use Illuminate\Http\Request;
use Biigle\Http\Controllers\Api\Controller;
use Illuminate\Validation\ValidationException;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Modules\Ananas\Notifications\AnnotationAssistanceResponse as ResponseNotification;

class AnnotationAssistanceRequestController extends Controller
{
    /**
     * @apiDefine ananasOwner Creator of the assistance request
     * The user must be the creator of the annotation assistance request.
     */

    /**
     * Create a new annotation assistance request
     *
     * @api {post} annotation-assistance-requests Create a new annotation assistance request
     * @apiGroup AnnotationAssistanceRequests
     * @apiName StoreAnnotationAssistanceRequests
     * @apiPermission projectMember
     *
     * @apiParam (Required arguments) {Number} annotation_id ID of the annotation to which the assistance request should belong.
     * @apiParam (Required arguments) {String} email Email address to which the assistance request should be sent.
     * @apiParam (Required arguments) {String} request_text Text with a short explanation or question that is shown to the receiver of the assistance request.
     *
     * @apiParam (Optional arguments) {Array} request_labels Array of label IDs that should be suggested to the receiver of the assistance request.
     *
     * @apiParamExample {String} Request example:
     * annotation_id: 123
     * email: 'joe@user.com'
     * request_text: 'Hi Joe, is this a Holothuroidea?'
     * request_labels: [55, 56]
     *
     * @param Request $request
     * @param int $id Volume ID
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, AnnotationAssistanceRequest::$createRules);
        $annotation = Annotation::with('image')->findOrFail($request->input('annotation_id'));
        $this->authorize('update', $annotation);
        $user = $request->user();

        $rateLimit = AnnotationAssistanceRequest::where('user_id', $user->id)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->exists();

        if ($rateLimit) {
            throw ValidationException::withMessages([
                'email' => 'You are not allowed to send more than one assistance request per minute.',
            ]);
        }

        $ananas = new AnnotationAssistanceRequest;
        $ananas->token = AnnotationAssistanceRequest::generateToken();
        $ananas->email = $request->input('email');
        $ananas->request_text = $request->input('request_text');
        $ananas->annotation_id = $request->input('annotation_id');
        $ananas->user()->associate($user);

        if ($request->filled('request_labels')) {
            // Check if the user has access to all labels they want to suggest for this
            // assistance request.

            // Array of all project IDs that the user and the annotation have in common
            // and where the user is editor, expert or admin.
            $projectIds = Project::inCommon($user, $annotation->image->volume_id)->pluck('id');

            $labels = Label::select('id', 'name', 'color')
                ->whereIn('id', $request->input('request_labels'))
                ->whereIn('label_tree_id', function ($query) use ($projectIds) {
                    $query->select('label_tree_id')
                        ->from('label_tree_project')
                        ->whereIn('label_tree_project.project_id', $projectIds);
                })
                ->get();

            if ($labels->count() !== count($request->input('request_labels'))) {
                throw ValidationException::withMessages([
                    'request_labels' => 'Some request labels belong to label trees that are not available for the annotation.',
                ]);
            }

            $ananas->request_labels = $labels;
        }

        $ananas->save();

        if (static::isAutomatedRequest($request)) {
            return $ananas;
        }

        return redirect()->route('show-assistance-request', $ananas->id);
    }

    /**
     * Close an annotation assistance request
     *
     * @api {put} annotation-assistance-requests/:token
     * @apiGroup AnnotationAssistanceRequests
     * @apiName UpdateAnnotationAssistanceRequests
     *
     * @apiParam {String} token The token that is associated with the assistance request.
     *
     * @apiParam (Optional arguments) {String} response_text Text with the response of the receiver of the assistance request.
     * @apiParam (Optional arguments) {Number} response_label_id ID of the label that the receiver of the assistance request chose.
     *
     * @apiDescription Either `response_text` or `response_label_id` or both must be specified to close an assistance request.
     *
     * @param Request $request
     * @param string $token Token of the assistance request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $token)
    {
        $this->validate($request, AnnotationAssistanceRequest::$updateRules);

        $ananas = AnnotationAssistanceRequest::where('token', $token)
            ->whereNull('closed_at')
            ->firstOrFail();

        if ($request->filled('response_label_id')) {
            $id = $request->input('response_label_id');
            $labels = collect($ananas->request_labels);
            if (!$labels->pluck('id')->containsStrict($id)) {
                throw ValidationException::withMessages([
                    'response_label_id' => ['The response label ID must be picked from one of the request labels.'],
                ]);
            }

            $ananas->response_label_id = $id;
        }

        $ananas->response_text = $request->input('response_text');
        $ananas->closed_at = new Carbon;
        $ananas->save();
        $ananas->user->notify(new ResponseNotification($ananas));
    }

    /**
     * Delete an annotation assistance request
     *
     * @api {delete} annotation-assistance-requests/:id
     * @apiGroup AnnotationAssistanceRequests
     * @apiName DestroyAnnotationAssistanceRequests
     * @apiPermission ananasOwner
     *
     * @apiParam {Number} id ID of the assistance request
     *
     * @param Request $request
     * @param int $id ID of the assistance request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $ananas = AnnotationAssistanceRequest::findOrFail($id);
        $this->authorize('destroy', $ananas);
        $ananas->delete();

        if (!static::isAutomatedRequest($request)) {
            return redirect()
                ->route('home')
                ->with('message', 'Annotation assistance request was deleted')
                ->with('messageType', 'success');
        }
    }
}

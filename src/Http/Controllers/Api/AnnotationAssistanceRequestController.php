<?php

namespace Biigle\Modules\Ananas\Http\Controllers\Api;

use Biigle\Annotation;
use Biigle\Http\Controllers\Api\Controller;
use Biigle\Label;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Modules\Ananas\Http\Requests\StoreAnnotationAssistanceRequest;
use Biigle\Modules\Ananas\Http\Requests\UpdateAnnotationAssistanceRequest;
use Biigle\Modules\Ananas\Notifications\AnnotationAssistanceResponse as ResponseNotification;
use Biigle\Project;
use Biigle\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnnotationAssistanceRequestController extends Controller
{
    /**
     * @apiDefine ananasOwner Creator of the assistance request
     * The user must be the creator of the annotation assistance request.
     */

    /**
     * Create a new annotation assistance request.
     *
     * @api {post} annotation-assistance-requests Create an assistance request
     * @apiGroup Annotation_Assistance_Requests
     * @apiName StoreAnnotationAssistanceRequests
     * @apiPermission projectMember
     *
     * @apiParam (Required arguments) {Number} annotation_id ID of the annotation to which the assistance request should belong.
     * @apiParam (Required arguments) {String} request_text Text with a short explanation or question that is shown to the receiver of the assistance request.
     *
     * @apiParam (Optional arguments) {Number} receiver_id ID of the Biigle user who should get an automatic notification on the assistance request.
     * @apiParam (Optional arguments) {Array} request_labels Array of label IDs that should be suggested to the receiver of the assistance request.
     *
     * @apiParamExample {String} Request example:
     * annotation_id: 123
     * receiver_id: 12
     * request_text: 'Hi Joe, is this a Holothuroidea?'
     * request_labels: [55, 56]
     *
     * @param StoreAnnotationAssistanceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnnotationAssistanceRequest $request)
    {
        $ananas = new AnnotationAssistanceRequest;
        $ananas->token = AnnotationAssistanceRequest::generateToken();
        $ananas->receiver_id = $request->input('receiver_id');
        $ananas->request_text = $request->input('request_text');
        $ananas->annotation_id = $request->input('annotation_id');
        $ananas->user()->associate($request->user());

        if ($request->filled('request_labels')) {
            $ananas->request_labels = $request->input('request_labels');
        }

        $ananas->save();

        if ($this->isAutomatedRequest()) {
            return $ananas;
        }

        return $this->fuzzyRedirect('show-assistance-request', $ananas->id);
    }

    /**
     * Close an annotation assistance request.
     *
     * @api {put} annotation-assistance-requests/:token Close an assistance request
     * @apiGroup Annotation_Assistance_Requests
     * @apiName UpdateAnnotationAssistanceRequests
     *
     * @apiParam {String} token The token that is associated with the assistance request.
     *
     * @apiParam (Optional arguments) {String} response_text Text with the response of the receiver of the assistance request.
     * @apiParam (Optional arguments) {Number} response_label_id ID of the label that the receiver of the assistance request chose.
     *
     * @apiDescription Either `response_text` or `response_label_id` or both must be specified to close an assistance request.
     *
     * @param UpdateAnnotationAssistanceRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnnotationAssistanceRequest $request)
    {
        $ananas = $request->ananas;
        if ($request->filled('response_label_id')) {
            $ananas->response_label_id = $request->input('response_label_id');
        }

        $ananas->response_text = $request->input('response_text');
        $ananas->closed_at = new Carbon;
        $ananas->save();
        $ananas->user->notify(new ResponseNotification($ananas));
    }

    /**
     * Delete an annotation assistance request.
     *
     * @api {delete} annotation-assistance-requests/:id Delete an assistance request
     * @apiGroup Annotation_Assistance_Requests
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

        if (!$this->isAutomatedRequest()) {
            return $this->fuzzyRedirect('home')
                ->with('message', 'Annotation assistance request was deleted')
                ->with('messageType', 'success');
        }
    }
}

<?php

namespace Biigle\Modules\Ananas\Http\Controllers\Views;

use DB;
use Biigle\Role;
use Biigle\LabelTree;
use Biigle\Annotation;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Biigle\Http\Controllers\Views\Controller;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;

class AnnotationAssistanceRequestController extends Controller
{
    /**
     * Create a new annotation assistance request
     *
     * @param Guard $auth
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Guard $auth, Request $request)
    {
        $annotation = Annotation::findOrFail($request->input('annotation_id'));
        $this->authorize('add-annotation', $annotation->image);

        $user = $auth->user();

        if ($user->isAdmin) {
            // admins have no restrictions
            $projectIds = DB::table('project_volume')
                ->where('volume_id', $annotation->image->volume_id)
                ->pluck('project_id');
        } else {
            // array of all project IDs that the user and the image have in common
            // and where the user is editor or admin
            $projectIds = DB::table('project_user')
                ->where('user_id', $user->id)
                ->whereIn('project_id', function ($query) use ($annotation) {
                    $query->select('project_volume.project_id')
                        ->from('project_volume')
                        ->join('project_user', 'project_volume.project_id', '=', 'project_user.project_id')
                        ->where('project_volume.volume_id', $annotation->image->volume_id)
                        ->whereIn('project_user.project_role_id', [Role::$editor->id, Role::$admin->id]);
                })
                ->pluck('project_id');
        }

        // all label trees that are used by all projects which are visible to the user
        $labelTrees = LabelTree::with('labels')
            ->select('id', 'name')
            ->whereIn('id', function ($query) use ($projectIds) {
                $query->select('label_tree_id')
                    ->from('label_tree_project')
                    ->whereIn('project_id', $projectIds);
            })
            ->get();

        return view('ananas::create', [
            'annotation' => $annotation,
            'labelTrees' => $labelTrees,
        ]);
    }

    /**
     * Show an assistance request
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $request = AnnotationAssistanceRequest::with('annotation.image.volume', 'annotation.shape')->findOrFail($id);
        $this->authorize('access', $request);

        return view('ananas::show', [
            'request' => $request,
        ]);
    }

    /**
     * Respond to an assistance request
     *
     * @param string $token
     *
     * @return \Illuminate\Http\Response
     */
    public function respond($token)
    {
        // TODO
    }
}
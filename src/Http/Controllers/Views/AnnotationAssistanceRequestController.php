<?php

namespace Biigle\Modules\Ananas\Http\Controllers\Views;

use Biigle\Enums\Role;
use Biigle\Enums\Shape;
use Biigle\Http\Controllers\Views\Controller;
use Biigle\ImageAnnotation;
use Biigle\Label;
use Biigle\LabelTree;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Project;
use Biigle\Services\AnnotationGuidelineService;
use DB;
use Illuminate\Http\Request;
use Storage;

class AnnotationAssistanceRequestController extends Controller
{
    /**
     * Create a new annotation assistance request.
     *
     * @param Request $request
     * @param AnnotationGuidelineService $guidelineService
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, AnnotationGuidelineService $guidelineService)
    {
        $annotation = ImageAnnotation::findOrFail($request->input('annotation_id'));
        $this->authorize('add-annotation', $annotation->image);

        $user = $request->user();

        if ($user->can('sudo')) {
            // Global admins have no restrictions.
            $projectIds = DB::table('project_volume')
                ->where('volume_id', $annotation->image->volume_id)
                ->pluck('project_id');
        } else {
            // Array of all project IDs that the user and the image have in common
            // and where the user is editor, expert or admin.
            $projectIds = Project::inCommon($user, $annotation->image->volume_id, [
                Role::EDITOR,
                Role::EXPERT,
                Role::ADMIN,
            ])->pluck('id');
        }

        // All label trees that are used by all projects in which the user can edit in.
        $labelTrees = LabelTree::select('id', 'name', 'version_id')
            ->with('labels', 'version')
            ->whereIn('id', function ($query) use ($projectIds) {
                $query->select('label_tree_id')
                    ->from('label_tree_project')
                    ->whereIn('project_id', $projectIds);
            })
            ->get();

        $volumeId = $annotation->image->volume_id;
        $annotationGuidelines = $guidelineService->getGuidelines($user, $volumeId)
            ->load('labels', 'project:id,name');
        $mustUseAnnotationGuideline = $annotationGuidelines->isNotEmpty()
            && $guidelineService->mustUseGuideline($user, $volumeId);

        return view('ananas::create', [
            'annotation' => $annotation,
            'labelTrees' => $labelTrees,
            'volumeId' => $volumeId,
            'annotationShape' => $annotation->shape->label(),
            'annotationGuidelines' => $annotationGuidelines,
            'mustUseAnnotationGuideline' => $mustUseAnnotationGuideline,
            'shapes' => Shape::pluckById(except: Shape::WHOLE_FRAME),
        ]);
    }

    /**
     * Show an assistance request.
     *
     * @param Request $httpRequest
     * @param AnnotationGuidelineService $guidelineService
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $httpRequest, AnnotationGuidelineService $guidelineService, $id)
    {
        $request = AnnotationAssistanceRequest::findOrFail($id);
        $this->authorize('access', $request);
        $request->load('annotation.image.volume', 'receiver');

        $annotation = collect($request->annotation->toArray())
            ->only('id', 'shape', 'points', 'image_id');
        // Preprocess the shape name for usage in the JS client.
        $annotation['shape'] = $request->annotation->shape->label();

        $responseLabelExists = Label::where('id', $request->response_label_id)->exists();

        $existingLabels = $request->annotation->labels()
            ->without('user', 'label')
            ->select('label_id', 'user_id')
            ->get();

        $tilesUriTemplate = Storage::disk(config('image.tiles.disk'))->url(':uuid/');

        $user = $httpRequest->user();
        $volumeId = $request->annotation->image->volume_id;

        // Guidelines are only needed to attach the response label. Guidelines that are
        // not enforced are included anyway because the guideline selector would
        // otherwise discard the stored guideline choice of the volume.
        if ($responseLabelExists) {
            $annotationGuidelines = $guidelineService->getGuidelines($user, $volumeId)
                ->load('labels', 'project:id,name');
            $mustUseAnnotationGuideline = $annotationGuidelines->isNotEmpty()
                && $guidelineService->mustUseGuideline($user, $volumeId);
        } else {
            $annotationGuidelines = collect([]);
            $mustUseAnnotationGuideline = false;
        }

        $shapes = Shape::pluckById(except: Shape::WHOLE_FRAME);

        return view('ananas::show', compact(
            'request',
            'annotation',
            'responseLabelExists',
            'existingLabels',
            'tilesUriTemplate',
            'volumeId',
            'annotationGuidelines',
            'mustUseAnnotationGuideline',
            'shapes'
        ));
    }

    /**
     * Respond to an assistance request.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\Response
     */
    public function respond($token)
    {
        $request = AnnotationAssistanceRequest::where('token', $token)
            ->whereNull('closed_at')
            ->with('annotation.image.volume')
            ->first();

        if (!$request) {
            return response()->view('ananas::respond-not-found', [], 404);
        }

        $annotation = collect($request->annotation->toArray())->only('shape', 'points');
        // Hide the actual annotation ID from the external user.
        $annotation['id'] = 0;
        // Preprocess the shape name for usage in the JS client.
        $annotation['shape'] = $request->annotation->shape->label();

        $tilesUriTemplate = Storage::disk(config('image.tiles.disk'))->url(':uuid/');

        return view('ananas::respond', [
            'request' => $request,
            'annotation' => $annotation,
            'tilesUriTemplate' => $tilesUriTemplate,
        ]);
    }

    /**
     * Show the list of all assistance requests of the user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->input('t');
        if (!in_array($type, ['open', 'closed', null])) {
            $type = null;
        }

        $requests = AnnotationAssistanceRequest::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->when($type === 'open', function ($query) {
                return $query->whereNull('closed_at');
            })
            ->when($type === 'closed', function ($query) {
                return $query->whereNotNull('closed_at');
            })
            ->paginate(10);

        // Add the URL parameter to the paginator so the pagination links are
        // constructed properly.
        $requests->appends('t', $type);

        return view('ananas::index', compact('requests', 'type'));
    }
}

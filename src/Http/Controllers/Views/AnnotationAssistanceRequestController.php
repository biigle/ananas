<?php

namespace Biigle\Modules\Ananas\Http\Controllers\Views;

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
        $this->authorize('update', $annotation);

        return view('ananas::create');
    }
}

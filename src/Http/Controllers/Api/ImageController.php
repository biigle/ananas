<?php

namespace Biigle\Modules\Ananas\Http\Controllers\Api;

use Biigle\Http\Controllers\Api\Controller;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;

class ImageController extends Controller
{
    /**
     * Shows the image file that belongs to the assistance request.
     *
     * @param string $token Token of the assistance request
     *
     * @return \Illuminate\Http\Response
     */
    public function show($token)
    {
        $request = AnnotationAssistanceRequest::where('token', $token)
            ->whereNull('closed_at')
            ->with('annotation.image')
            ->firstOrFail();

        return $request->annotation->image->getFile();
    }
}

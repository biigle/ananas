<?php

namespace Biigle\Modules\Ananas;

use Biigle\User;
use Biigle\Annotation;
use Illuminate\Database\Eloquent\Model;

/**
 * With an annotation assistance request a BIIGLE user can consult some externa
 * person for the label of a specific annotation.
 */
class AnnotationAssistanceRequest extends Model
{
    /**
     * Validation rules for creating a new volume.
     *
     * @var array
     */
    public static $createRules = [
        'email' => 'required|email',
        'request_text' => 'required',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'request_labels' => 'array',
        'response_label_id' => 'integer',
        'closed_at' => 'timestamp',
    ];

    /**
     * The user that created the assistance request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The annotation to which the assistance request belongs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function annotation()
    {
        return $this->belongsTo(Annotation::class);
    }
}

<?php

namespace Biigle\Modules\Ananas;

use Biigle\ImageAnnotation;
use Biigle\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * With an annotation assistance request a BIIGLE user can consult some externa
 * person for the label of a specific annotation.
 */
class AnnotationAssistanceRequest extends Model
{
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'request_labels' => 'array',
        'response_label_id' => 'integer',
        'closed_at' => 'datetime',
        'user_id' => 'integer',
        'receiver_id' => 'integer',
    ];

    /**
     * Generate a token for use in the annotation assistance request URL.
     *
     * @return string
     */
    public static function generateToken()
    {
        do {
            $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        } while (static::where('token', $token)->exists());

        return $token;
    }

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
     * The (optional) user that should receive the assistance request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
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
        return $this->belongsTo(ImageAnnotation::class);
    }

    /**
     * Get the chosen response label from the request labels array.
     *
     * @return array
     */
    public function getResponseLabelAttribute()
    {
        if ($this->response_label_id) {
            foreach ($this->request_labels as $label) {
                if ($label['id'] === $this->response_label_id) {
                    return $label;
                }
            }
        }

        return;
    }
}

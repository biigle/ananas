<?php

namespace Biigle\Modules\Ananas;

use Biigle\User;
use Biigle\Annotation;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * With an annotation assistance request a BIIGLE user can consult some externa
 * person for the label of a specific annotation.
 */
class AnnotationAssistanceRequest extends Model
{
    // If an assistance request is "notified", an email is sent to the receiver of the
    // request.
    use Notifiable;

    /**
     * Validation rules for creating a new assistance request.
     *
     * @var array
     */
    public static $createRules = [
        'annotation_id' => 'required|exists:annotations,id',
        'email' => 'required|email',
        'request_text' => 'required',
        'request_labels' => 'array',
    ];

    /**
     * Validation rules for updating/closing an assistance request.
     *
     * @var array
     */
    public static $updateRules = [
        'response_text' => 'required_without:response_label_id',
        'response_label_id' => 'required_without:response_text',
    ];

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
     * The annotation to which the assistance request belongs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function annotation()
    {
        return $this->belongsTo(Annotation::class);
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

        return null;
    }
}

<?php

namespace Biigle\Modules\Ananas\Http\Requests;

use Biigle\ImageAnnotation;
use Biigle\Label;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Project;
use Biigle\Role;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnnotationAssistanceRequest extends FormRequest
{
    /**
     * The annotation to create the request for.
     *
     * @var ImageAnnotation
     */
    public $annotation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->annotation);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'annotation_id' => 'required|integer|exists:image_annotations,id',
            'receiver_id' => 'nullable|integer|exists:users,id',
            'request_text' => 'required',
            'request_labels' => 'array',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->annotation = ImageAnnotation::find($this->input('annotation_id'));

        if ($this->filled('request_labels') && $this->annotation) {
            // Check if the user has access to all labels they want to suggest for this
            // assistance request.

            // Array of all project IDs that the user and the annotation have in common
            // and where the user is editor, expert or admin.
            $projectIds = Project::inCommon(
                $this->user(),
                $this->annotation->image->volume_id,
                [Role::editorId(), Role::expertId(), Role::adminId()]
            )->pluck('id');

            $labels = Label::select('id', 'name', 'color')
                ->whereIn('id', $this->input('request_labels'))
                ->whereIn('label_tree_id', function ($query) use ($projectIds) {
                    $query->select('label_tree_id')
                        ->from('label_tree_project')
                        ->whereIn('label_tree_project.project_id', $projectIds);
                })
                ->get()
                ->toArray();

            $this->merge([
                'original_request_labels' => $this->input('request_labels'),
                'request_labels' => $labels,
            ]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->rateLimitApplies()) {
                $validator->errors()->add('request_text', 'You are not allowed to send more than one assistance request per minute.');
            }

            if ($this->invalidLabels()) {
                $validator->errors()->add('request_labels', 'Some request labels belong to label trees that are not available for the annotation.');
            }
        });
    }

    /**
     * Check if the rate limit of creating new requests applies.
     *
     * @return bool
     */
    protected function rateLimitApplies()
    {
        return AnnotationAssistanceRequest::where('user_id', $this->user()->id)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->exists();
    }

    /**
     * Check if the user attempts to use invalid labels for the request.
     *
     * @return bool
     */
    protected function invalidLabels()
    {
        if ($this->filled('request_labels')) {
            return count($this->input('request_labels')) !== count($this->input('original_request_labels'));
        }

        return false;
    }
}

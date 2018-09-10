<?php

namespace Biigle\Modules\Ananas\Http\Requests;

use Biigle\Role;
use Biigle\Label;
use Carbon\Carbon;
use Biigle\Project;
use Biigle\Annotation;
use Illuminate\Foundation\Http\FormRequest;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;

class UpdateAnnotationAssistanceRequest extends FormRequest
{
    /**
     * The request to update.
     *
     * @var AnnotationAssistanceRequest
     */
    public $ananas;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'response_text' => 'required_without:response_label_id',
            'response_label_id' => 'required_without:response_text',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $this->ananas = AnnotationAssistanceRequest::where('token', $this->route('token'))
            ->whereNull('closed_at')
            ->firstOrFail();

        $validator->after(function ($validator) {
            if ($this->filled('response_label_id')) {
                $labelAllowed = collect($this->ananas->request_labels)
                    ->pluck('id')
                    ->containsStrict($this->input('response_label_id'));
                if (!$labelAllowed) {
                    $validator->errors()->add('response_label_id', 'The response label ID must be picked from one of the request labels.');
                }
            }
        });
    }
}

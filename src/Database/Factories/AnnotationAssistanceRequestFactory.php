<?php

namespace Biigle\Modules\Ananas\Database\Factories;

use Biigle\ImageAnnotation;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnotationAssistanceRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AnnotationAssistanceRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => $this->faker->sha256,
            'request_text' => $this->faker->text,
            'annotation_id' => ImageAnnotation::factory(),
            'user_id' => User::factory(),
        ];
    }
}

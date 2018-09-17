<?php

$factory->define(Biigle\Modules\Ananas\AnnotationAssistanceRequest::class, function ($faker) {
    return [
        'token' => $faker->sha256,
        'request_text' => $faker->text,
        'annotation_id' => function () {
            return factory(Biigle\Annotation::class)->create()->id;
        },
        'user_id' => function () {
            return factory(Biigle\User::class)->create()->id;
        },
    ];
});

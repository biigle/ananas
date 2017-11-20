<?php

namespace Biigle\Tests\Modules\Ananas\Http\Controllers\Views;

use ApiTestCase;
use Biigle\Tests\ImageTest;
use Biigle\Tests\AnnotationTest;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;

class AnnotationAssistanceRequestControllerTest extends ApiTestCase
{
    public function testCreate()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = AnnotationTest::create(['image_id' => $image->id]);
        $id = $annotation->id;


        $this->get("annotation-assistance-requests/create")->assertRedirect('login');

        $this->beGuest();
        $this->get("annotation-assistance-requests/create")->assertStatus(404);
        $this->get("annotation-assistance-requests/create?annotation_id={$id}")
            ->assertStatus(403);

        $this->beEditor();
        $this->get("annotation-assistance-requests/create?annotation_id={$id}")
            ->assertStatus(200)
            ->assertViewIs('ananas::create');
    }
}

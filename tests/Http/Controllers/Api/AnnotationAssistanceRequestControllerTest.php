<?php

namespace Biigle\Tests\Modules\Ananas\Http\Controllers\Api;

use ApiTestCase;
use Biigle\Tests\LabelTest;
use Biigle\Tests\ImageTest;
use Biigle\Tests\AnnotationTest;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;

class AnnotationAssistanceRequestControllerTest extends ApiTestCase
{
    public function testStore()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = AnnotationTest::create(['image_id' => $image->id]);

        $this->doTestApiRoute('POST', '/api/v1/annotation-assistance-requests');

        $this->beGuest();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests');
        // missing arguments
        $response->assertStatus(422);

        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => 9999,
            'email' => 'joe@user.com',
            'request_text' => 'Hi Joe!',
        ]);
        // annotation does not exist
        $response->assertStatus(422);

        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'email' => 'joe@user.com',
            'request_text' => 'Hi Joe!',
        ]);
        // no permissions as guest for that annotation
        $response->assertStatus(403);

        $this->assertNull(AnnotationAssistanceRequest::first());

        $this->beEditor();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'email' => 'joe@user.com',
            'request_text' => 'Hi Joe!',
            'request_labels' => [$this->labelRoot()->id, $this->labelChild()->id],
        ]);
        $response->assertStatus(200);

        $assistanceRequest =AnnotationAssistanceRequest::first();
        $this->assertEquals($annotation->id, $assistanceRequest->annotation_id);
        $this->assertEquals('joe@user.com', $assistanceRequest->email);
        $this->assertEquals('Hi Joe!', $assistanceRequest->request_text);
        $this->assertEquals($this->editor()->id, $assistanceRequest->user_id);
        $labels = [
            [
                'id' => $this->labelRoot()->id,
                'name' => $this->labelRoot()->name,
                'color' => $this->labelRoot()->color
            ],
            [
                'id' => $this->labelChild()->id,
                'name' => $this->labelChild()->name,
                'color' => $this->labelChild()->color
            ],
        ];
        $this->assertEquals($labels, $assistanceRequest->request_labels);
        $this->assertNotNull($assistanceRequest->token);
    }

    public function testStoreVerifyVolumeLabels()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = AnnotationTest::create(['image_id' => $image->id]);
        $label = LabelTest::create();

        $this->beEditor();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'email' => 'joe@user.com',
            'request_text' => 'Hi Joe!',
            'request_labels' => [$this->labelRoot()->id, $label->id],
        ]);
        // $label does not belong to any of the label trees associated with the
        // annotation
        $response->assertStatus(422);
    }

    public function testUpdate()
    {
        $this->markTestIncomplete();
    }

    public function testDestroy()
    {
        $this->markTestIncomplete();
    }
}

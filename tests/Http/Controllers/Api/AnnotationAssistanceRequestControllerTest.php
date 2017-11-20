<?php

namespace Biigle\Tests\Modules\Ananas\Http\Controllers\Api;

use ApiTestCase;
use Biigle\Tests\LabelTest;
use Biigle\Tests\ImageTest;
use Biigle\Tests\AnnotationTest;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;

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

    public function testStoreRedirect()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = AnnotationTest::create(['image_id' => $image->id]);
        $label = LabelTest::create();

        $this->beEditor();
        $response = $this->post('/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'email' => 'joe@user.com',
            'request_text' => 'Hi Joe!',
        ]);

        $ananas = AnnotationAssistanceRequest::first();
        $response->assertRedirect("annotation-assistance-requests/{$ananas->id}");
    }

    public function testUpdate()
    {
        $ananas = AnanasTest::create();
        $token = $ananas->token;

        $this->json('PUT', "/api/v1/annotation-assistance-requests/{$token}")
            ->assertStatus(422);

        $this->json('PUT', "/api/v1/annotation-assistance-requests/abcdef", [
            'response_text' => 'This is a stone.',
        ])
        ->assertStatus(404);

        $this->json('PUT', "/api/v1/annotation-assistance-requests/{$token}", [
            'response_text' => 'This is a stone.',
        ])
        ->assertStatus(200);

        $this->assertEquals('This is a stone.', $ananas->fresh()->response_text);

        $this->json('PUT', "/api/v1/annotation-assistance-requests/{$token}", [
            'response_text' => 'This is a stone.',
        ])
        ->assertStatus(404);

        $ananas = AnanasTest::create();
        $token = $ananas->token;

        $this->json('PUT', "/api/v1/annotation-assistance-requests/{$token}", [
            'response_label_id' => 9999,
        ])
        // Label ID must be from the request_labels array.
        ->assertStatus(422);

        $ananas->request_labels = [['id' => 9999]];
        $ananas->save();

        $this->json('PUT', "/api/v1/annotation-assistance-requests/{$token}", [
            'response_label_id' => 9999,
        ])
        ->assertStatus(200);

        $this->assertEquals(9999, $ananas->fresh()->response_label_id);

        $ananas = AnanasTest::create(['request_labels' => [['id' => 9999]]]);
        $token = $ananas->token;

        $this->json('PUT', "/api/v1/annotation-assistance-requests/{$token}", [
            'response_text' => 'This is a stone.',
            'response_label_id' => 9999,
        ])
        ->assertStatus(200);

        $this->assertEquals('This is a stone.', $ananas->fresh()->response_text);
        $this->assertEquals(9999, $ananas->fresh()->response_label_id);
    }

    public function testDestroy()
    {
        $ananas = AnanasTest::create();
        $id = $ananas->id;

        $this->doTestApiRoute('DELETE', "/api/v1/annotation-assistance-requests/{$id}");

        $this->beEditor();
        $this->json('DELETE', "/api/v1/annotation-assistance-requests/{$id}")
            ->assertStatus(403);

        $this->be($ananas->user);
        $this->json('DELETE', "/api/v1/annotation-assistance-requests/{$id}")
            ->assertStatus(200);

        $this->assertNull($ananas->fresh());
    }
}
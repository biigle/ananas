<?php

namespace Biigle\Tests\Modules\Ananas\Http\Controllers\Api;

use ApiTestCase;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Modules\Ananas\Notifications\AnnotationAssistanceResponse as ResponseNotification;
use Biigle\Tests\ImageAnnotationTest;
use Biigle\Tests\ImageTest;
use Biigle\Tests\LabelTest;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;
use Illuminate\Support\Facades\Notification;

class AnnotationAssistanceRequestControllerTest extends ApiTestCase
{
    public function testStore()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = ImageAnnotationTest::create(['image_id' => $image->id]);

        $this->doTestApiRoute('POST', '/api/v1/annotation-assistance-requests');

        $this->beGuest();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests');
        // Missing arguments. Not authorized to use an unknown annotation.
        $response->assertStatus(403);

        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => 9999,
            'request_text' => 'Hi Joe!',
        ]);
        // Annotation does not exist. Not authorized to use an unknown annotation.
        $response->assertStatus(403);

        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'request_text' => 'Hi Joe!',
        ]);
        // No permissions as guest for that annotation.
        $response->assertStatus(403);

        $this->assertNull(AnnotationAssistanceRequest::first());

        $this->beEditor();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'request_text' => 'Hi Joe!',
            'request_labels' => [$this->labelRoot()->id, $this->labelChild()->id],
        ]);
        $response->assertSuccessful();

        $assistanceRequest = AnnotationAssistanceRequest::first();
        $this->assertEquals($annotation->id, $assistanceRequest->annotation_id);
        $this->assertEquals('Hi Joe!', $assistanceRequest->request_text);
        $this->assertEquals($this->editor()->id, $assistanceRequest->user_id);
        $labels = [
            [
                'id' => $this->labelRoot()->id,
                'name' => $this->labelRoot()->name,
                'color' => $this->labelRoot()->color,
            ],
            [
                'id' => $this->labelChild()->id,
                'name' => $this->labelChild()->name,
                'color' => $this->labelChild()->color,
            ],
        ];
        $this->assertEquals($labels, $assistanceRequest->request_labels);
        $this->assertNotNull($assistanceRequest->token);
    }

    public function testStoreReceiver()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = ImageAnnotationTest::create(['image_id' => $image->id]);

        $this->beEditor();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'receiver_id' => $this->user()->id,
            'request_text' => 'Hi Joe!',
        ]);
        $response->assertSuccessful();
        $assistanceRequest = AnnotationAssistanceRequest::first();
        $this->assertEquals($this->user()->id, $assistanceRequest->receiver_id);
    }

    public function testStoreRateLimiting()
    {
        $ananas = AnanasTest::create(['user_id' => $this->editor()->id]);

        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = ImageAnnotationTest::create(['image_id' => $image->id]);

        $this->beEditor();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'request_text' => 'Hi Joe!',
        ]);
        // Denied because User already created a request within the last minute.
        $response->assertStatus(422);

        $ananas->created_at = (new \Carbon\Carbon)->subSeconds(61);
        $ananas->save();

        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
            'request_text' => 'Hi Joe!',
        ]);
        $response->assertSuccessful();
    }

    public function testStoreVerifyVolumeLabels()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = ImageAnnotationTest::create(['image_id' => $image->id]);
        $label = LabelTest::create();

        $this->beEditor();
        $response = $this->json('POST', '/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
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
        $annotation = ImageAnnotationTest::create(['image_id' => $image->id]);
        $label = LabelTest::create();

        $this->beEditor();
        $response = $this->post('/api/v1/annotation-assistance-requests', [
            'annotation_id' => $annotation->id,
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

        $this->json('PUT', '/api/v1/annotation-assistance-requests/abcdef', [
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

    public function testUpdateNotification()
    {
        $ananas = AnanasTest::create(['request_labels' => [['id' => 9999]]]);
        $token = $ananas->token;

        Notification::fake();
        $this->json('PUT', "/api/v1/annotation-assistance-requests/{$token}", [
                'response_label_id' => 9999,
            ])
            ->assertStatus(200);

        Notification::assertSentTo($ananas->user, ResponseNotification::class);
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

    public function testDestroyFormRequest()
    {
        $ananas = AnanasTest::create();
        $id = $ananas->id;

        $this->be($ananas->user);
        $this->delete("/api/v1/annotation-assistance-requests/{$id}")
            ->assertRedirect('/')
            ->assertSessionHas('message', 'Annotation assistance request was deleted');

        $this->assertNull($ananas->fresh());
    }
}

<?php

namespace Biigle\Tests\Modules\Ananas\Http\Controllers\Api;

use ApiTestCase;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;

class ImageControllerTest extends ApiTestCase
{
    public function testShow()
    {
        $token = AnanasTest::create()->token;

        $response = $this->get("/api/v1/annotation-assistance-requests/abcdef/image");
        $response->assertStatus(404);

        $response = $this->get("/api/v1/annotation-assistance-requests/{$token}/image");
        $response->assertStatus(200);
        $this->assertEquals('image/jpeg', $response->headers->get('content-type'));
    }
}

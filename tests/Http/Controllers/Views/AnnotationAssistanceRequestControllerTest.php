<?php

namespace Biigle\Tests\Modules\Ananas\Http\Controllers\Views;

use ApiTestCase;
use Biigle\Tests\ImageAnnotationTest;
use Biigle\Tests\ImageTest;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;

class AnnotationAssistanceRequestControllerTest extends ApiTestCase
{
    public function testCreate()
    {
        $image = ImageTest::create(['volume_id' => $this->volume()->id]);
        $annotation = ImageAnnotationTest::create(['image_id' => $image->id]);
        $id = $annotation->id;

        $this->get('annotation-assistance-requests/create')->assertRedirect('login');

        $this->beGuest();
        $this->get('annotation-assistance-requests/create')->assertStatus(404);
        $this->get("annotation-assistance-requests/create?annotation_id={$id}")
            ->assertStatus(403);

        $this->beEditor();
        $this->get("annotation-assistance-requests/create?annotation_id={$id}")
            ->assertStatus(200)
            ->assertViewIs('ananas::create');
    }

    public function testRespond()
    {
        $request = AnanasTest::create();

        $this->get('annotation-assistance-requests/respond/abcdef')
            ->assertStatus(404)
            ->assertViewIs('ananas::respond-not-found');

        $this->get("annotation-assistance-requests/respond/{$request->token}")
            ->assertStatus(200)
            ->assertViewIs('ananas::respond')
            // Test if "secret" information like IDs is hidden from the view.
            ->assertViewHas('annotation', collect([
                'id' => 0,
                'shape' => $request->annotation->shape->name,
                'points' => $request->annotation->points,
            ]));

        $request->closed_at = new \Carbon\Carbon;
        $request->save();

        $this->get("annotation-assistance-requests/respond/{$request->token}")
            ->assertStatus(404);
    }

    public function testShow()
    {
        $request = AnanasTest::create();

        $this->get("annotation-assistance-requests/{$request->id}")
            ->assertRedirect('login');

        $this->beAdmin();
        $this->get('annotation-assistance-requests/99999')->assertStatus(404);
        $this->get("annotation-assistance-requests/{$request->id}")
            ->assertStatus(403);

        $this->be($request->user);
        $this->get("annotation-assistance-requests/{$request->id}")
            ->assertStatus(200)
            ->assertViewIs('ananas::show');
    }

    public function testIndex()
    {
        $request = AnanasTest::create();

        $this->get('annotation-assistance-requests')
            ->assertRedirect('login');

        $this->beUser();
        $this->get('annotation-assistance-requests')
            ->assertStatus(200)
            ->assertViewIs('ananas::index')
            ->assertDontSeeText($request->request_text);

        $this->be($request->user);
        $this->get('annotation-assistance-requests')
            ->assertStatus(200)
            ->assertViewIs('ananas::index')
            ->assertSeeText($request->request_text);

        $this->get('annotation-assistance-requests?t=open')
            ->assertStatus(200)
            ->assertViewIs('ananas::index')
            ->assertSeeText($request->request_text);

        $this->get('annotation-assistance-requests?t=closed')
            ->assertStatus(200)
            ->assertViewIs('ananas::index')
            ->assertDontSeeText($request->request_text);

        $request->closed_at = new \Carbon\Carbon;
        $request->save();

        $this->get('annotation-assistance-requests?t=closed')
            ->assertStatus(200)
            ->assertViewIs('ananas::index')
            ->assertSeeText($request->request_text);
    }
}

<?php

namespace Biigle\Tests\Modules\Ananas;

use Biigle\Modules\Ananas\AnnotationAssistanceRequest;
use Biigle\Tests\UserTest;
use Illuminate\Database\QueryException;
use ModelTestCase;

class AnnotationAssistanceRequestTest extends ModelTestCase
{
    /**
     * The model class this class will test.
     */
    protected static $modelClass = AnnotationAssistanceRequest::class;

    public function testAttributes()
    {
        $this->assertNotNull($this->model->token);
        $this->assertNotNull($this->model->request_text);
        $this->assertNotNull($this->model->annotation_id);
        $this->assertNotNull($this->model->user_id);
        $this->assertNotNull($this->model->created_at);
        $this->assertNotNull($this->model->updated_at);
    }

    public function testNameRequired()
    {
        $this->model->token = null;
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testRequestTextRequired()
    {
        $this->model->request_text = null;
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testAnnotationRequired()
    {
        $this->model->annotation()->dissociate();
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testUserRequired()
    {
        $this->model->user()->dissociate();
        $this->expectException(QueryException::class);
        $this->model->save();
    }

    public function testReceiverOptional()
    {
        $this->assertNull($this->model->reveiver_id);
        $this->model->receiver()->associate(UserTest::create());
        $this->model->save();
        $this->assertNotNull($this->model->fresh()->receiver_id);
    }

    public function testGenerateToken()
    {
        $token = AnnotationAssistanceRequest::generateToken();
        $this->assertSame(64, strlen($token));
    }

    public function testGetResponseLabelAttribute()
    {
        $this->model->request_labels = [['id' => 2, 'name' => 'my label']];

        $this->assertNull($this->model->response_label);
        $this->model->response_label_id = 1;
        $this->assertNull($this->model->response_label);
        $this->model->response_label_id = 2;
        $this->assertSame(['id' => 2, 'name' => 'my label'], $this->model->response_label);
    }
}

<?php

namespace Biigle\Tests\Modules\Ananas;

use ModelTestCase;
use Biigle\Modules\Ananas\AnnotationAssistanceRequest;

class AnnotationAssistanceRequestTest extends ModelTestCase
{
    /**
     * The model class this class will test.
     */
    protected static $modelClass = AnnotationAssistanceRequest::class;

    public function testAttributes()
    {
        $this->assertNotNull($this->model->token);
        $this->assertNotNull($this->model->email);
        $this->assertNotNull($this->model->request_text);
        $this->assertNotNull($this->model->annotation_id);
        $this->assertNotNull($this->model->user_id);
        $this->assertNotNull($this->model->created_at);
        $this->assertNotNull($this->model->updated_at);
    }

    public function testNameRequired()
    {
        $this->model->token = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testEmailRequired()
    {
        $this->model->email = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testRequestTextRequired()
    {
        $this->model->request_text = null;
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testAnnotationRequired()
    {
        $this->model->annotation()->dissociate();
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testUserRequired()
    {
        $this->model->user()->dissociate();
        $this->setExpectedException('Illuminate\Database\QueryException');
        $this->model->save();
    }

    public function testGenerateToken()
    {
        $token = AnnotationAssistanceRequest::generateToken();
        $this->assertEquals(64, strlen($token));
    }

    public function testGetResponseLabelAttribute()
    {
        $this->model->request_labels = [['id' => 2, 'name' => 'my label']];

        $this->assertNull($this->model->response_label);
        $this->model->response_label_id = 1;
        $this->assertNull($this->model->response_label);
        $this->model->response_label_id = 2;
        $this->assertEquals(['id' => 2, 'name' => 'my label'], $this->model->response_label);
    }
}

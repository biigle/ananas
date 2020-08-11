<?php

namespace Biigle\Tests\Modules\Ananas\Http\Requests;

use ApiTestCase;

class UpdateUserSettingsTest extends ApiTestCase
{
    public function testUpdate()
    {
        $this->beUser();
        $this->putJson("/api/v1/users/my/settings", ['ananas_notifications' => 'test'])
            ->assertStatus(422);

        $this->assertNull($this->user()->fresh()->getSettings('ananas_notifications'));

        $this->putJson("/api/v1/users/my/settings", ['ananas_notifications' => 'email'])
            ->assertStatus(200);

        $this->assertEquals('email', $this->user()->fresh()->getSettings('ananas_notifications'));

        $this->putJson("/api/v1/users/my/settings", ['ananas_notifications' => 'web'])
            ->assertStatus(200);

        $this->assertEquals('web', $this->user()->fresh()->getSettings('ananas_notifications'));
    }
}

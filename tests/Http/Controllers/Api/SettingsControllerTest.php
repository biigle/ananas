<?php

namespace Biigle\Tests\Modules\Ananas\Http\Controllers;

use ApiTestCase;

class SettingsControllerTest extends ApiTestCase
{
    public function testStore()
    {
        $response = $this->json('POST', 'api/v1/users/my/settings/ananas')
            ->assertStatus(401);

        $this->beUser();
        $response = $this->post('api/v1/users/my/settings/ananas')
            ->assertStatus(200);

        $this->assertNull($this->user()->fresh()->settings);

        $response = $this->post('api/v1/users/my/settings/ananas', [
                'unknown_key' => 'somevalue',
            ])
            ->assertStatus(200);

        $this->assertNull($this->user()->fresh()->settings);
    }

    public function testStoreNotificationSettings()
    {
        $user = $this->user();
        $this->beUser();

        $this->assertNull($this->user()->fresh()->getSettings('ananas_notifications'));

        $response = $this->json('POST', 'api/v1/users/my/settings/ananas', [
                'ananas_notifications' => 'unknown value',
            ])
            ->assertStatus(422);

        $this->assertNull($this->user()->fresh()->getSettings('ananas_notifications'));

        $response = $this->json('POST', 'api/v1/users/my/settings/ananas', [
                'ananas_notifications' => 'email',
            ])
            ->assertStatus(200);

        $this->assertEquals('email', $this->user()->fresh()->getSettings('ananas_notifications'));

        $response = $this->json('POST', 'api/v1/users/my/settings/ananas', [
                'ananas_notifications' => 'web',
            ])
            ->assertStatus(200);

        $this->assertEquals('web', $this->user()->fresh()->getSettings('ananas_notifications'));

        config(['ananas.notifications.allow_user_settings' => false]);

        $response = $this->json('POST', 'api/v1/users/my/settings/ananas', [
                'ananas_notifications' => 'email',
            ])
            ->assertStatus(404);

        $this->assertEquals('web', $this->user()->fresh()->getSettings('ananas_notifications'));
    }
}

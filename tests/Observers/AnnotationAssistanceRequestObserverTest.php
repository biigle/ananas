<?php

namespace Biigle\Tests\Modules\Ananas;

use TestCase;
use Biigle\User;
use Illuminate\Support\Facades\Notification;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;
use Biigle\Modules\Ananas\Notifications\AnnotationAssistanceRequest as AnanasNotification;

class AnnotationAssistanceRequestObserverTest extends TestCase
{
    public function testSaved()
    {
        Notification::fake();
        $ananas = AnanasTest::create();
        $tempUser = (new User)->forceFill(['email' => $ananas->email]);

        Notification::assertSentTo($tempUser, AnanasNotification::class, function ($notification, $channels) use ($ananas, $tempUser) {
                return $notification->request->token === $ananas->token;
            }
        );
    }
}

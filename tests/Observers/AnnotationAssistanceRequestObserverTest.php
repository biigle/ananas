<?php

namespace Biigle\Tests\Modules\Ananas\Observers;

use TestCase;
use Biigle\Tests\UserTest;
use Illuminate\Support\Facades\Notification;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;
use Biigle\Modules\Ananas\Notifications\AnnotationAssistanceRequest as AnanasNotification;

class AnnotationAssistanceRequestObserverTest extends TestCase
{
    public function testSavedWithoutReceiver()
    {
        Notification::fake();
        $ananas = AnanasTest::create();
        Notification::assertNothingSent();
    }

    public function testSavedWithReceiver()
    {
        Notification::fake();
        $receiver = UserTest::create();
        $ananas = AnanasTest::create(['receiver_id' => $receiver->id]);
        Notification::assertSentTo($receiver, AnanasNotification::class, function ($n) use ($ananas) {
            return $n->request === $ananas;
        });
    }
}

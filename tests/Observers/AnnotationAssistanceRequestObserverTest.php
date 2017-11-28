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
        Notification::assertSentTo($ananas, AnanasNotification::class);
    }
}

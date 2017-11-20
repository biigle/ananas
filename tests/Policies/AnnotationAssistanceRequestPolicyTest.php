<?php

namespace Biigle\TestsModules\Ananas\Policies;

use TestCase;
use Biigle\Role;
use Biigle\Tests\UserTest;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;

class AnnotationAssistanceRequestPolicyTest extends TestCase
{
    public function testDestroy()
    {
        $ananas = AnanasTest::create();
        $user = UserTest::create();
        $admin = UserTest::create(['role_id' => Role::$admin->id]);

        $this->assertFalse($user->can('destroy', $ananas));
        $this->assertTrue($admin->can('destroy', $ananas));
        $this->assertTrue($ananas->user->can('destroy', $ananas));
    }
}

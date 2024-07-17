<?php

namespace Biigle\Tests\Modules\Ananas\Policies;

use Biigle\Role;
use Biigle\Tests\Modules\Ananas\AnnotationAssistanceRequestTest as AnanasTest;
use Biigle\Tests\UserTest;
use TestCase;

class AnnotationAssistanceRequestPolicyTest extends TestCase
{
    public function testAccess()
    {
        $ananas = AnanasTest::create();
        $user = UserTest::create();
        $admin = UserTest::create(['role_id' => Role::adminId()]);

        $this->assertFalse($user->can('access', $ananas));
        $this->assertTrue($admin->can('access', $ananas));
        $this->assertTrue($ananas->user->can('access', $ananas));
    }

    public function testDestroy()
    {
        $ananas = AnanasTest::create();
        $user = UserTest::create();
        $admin = UserTest::create(['role_id' => Role::adminId()]);

        $this->assertFalse($user->can('destroy', $ananas));
        $this->assertTrue($admin->can('destroy', $ananas));
        $this->assertTrue($ananas->user->can('destroy', $ananas));
    }
}

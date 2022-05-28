<?php

namespace Bfg\Comcode\Tests;

class CCClassConstTest extends TestCase
{
    public function test_class_public_const()
    {
        $class = $this->class();
        $class->publicConst('test1');
        $class->publicConst('test2', 1);
        $class->save();
        $this->assertClassContains('public const TEST1 = null;');
        $this->assertClassContains('public const TEST2 = 1;');
        $class->forgetConst('test1');
        $class->forgetConst('test2');
        $class->save();
        $this->assertClassNotContains('public const TEST1 = null;');
        $this->assertClassNotContains('public const TEST2 = 1;');
        $this->class()->delete();
    }

    public function test_class_protected_const()
    {
        $class = $this->class();
        $class->protectedConst('test1');
        $class->protectedConst('test2', 1);
        $class->save();
        $this->assertClassContains('protected const TEST1 = null;');
        $this->assertClassContains('protected const TEST2 = 1;');
        $class->forgetConst('test1');
        $class->forgetConst('test2');
        $class->save();
        $this->assertClassNotContains('protected const TEST1 = null;');
        $this->assertClassNotContains('protected const TEST2 = 1;');
        $this->class()->delete();
    }
}

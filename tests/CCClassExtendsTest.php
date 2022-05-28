<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;

class CCClassExtendsTest extends TestCase
{
    public function test_class_extends()
    {
        $this->class()->save();
        $this->assertClassContains('use Bfg\Comcode\Comcode;');
        $this->assertClassContains('extends Comcode');
        $this->class()->forgetExtends(Comcode::class);
        $this->class()->save();
        $this->assertClassNotContains('use Bfg\Comcode\Comcode;');
        $this->assertClassNotContains('extends Comcode');
        $this->class()->delete();
    }
}

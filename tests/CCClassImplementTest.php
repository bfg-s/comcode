<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Interfaces\AnonymousInterface;
use Traversable;

class CCClassImplementTest extends TestCase
{
    public function test_class_implements()
    {
        $this->class();
        $this->class()->implement(AnonymousInterface::class);
        $this->class()->save();
        $this->assertClassContains('implements AlwaysLastNodeInterface, AnonymousInterface');
        $this->class()->forgetImplement(AlwaysLastNodeInterface::class);
        $this->class()->forgetImplement(AnonymousInterface::class);
        $this->class()->save();
        $this->assertClassNotContains('implements');
        $this->class()->delete();
    }
}

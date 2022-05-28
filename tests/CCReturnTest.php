<?php

namespace Bfg\Comcode\Tests;

class CCReturnTest extends TestCase
{
    public function test_class_method_return()
    {
        $method = $this->class()->publicMethod('node');
        $method->return()->this();
        $this->class()->save();
        $this->assertClassContains('public function node()');
        $this->assertClassContains('return $this;');
        $method->forgetReturn();
        $this->class()->save();
        $this->assertClassNotContains('return $this;');
        $this->class()->delete();
    }

    public function test_class_method_return_value()
    {
        $method = $this->class()->publicMethod('node2');
        $method->return()->real(1);
        $this->class()->save();
        $this->assertClassContains('public function node2()');
        $this->assertClassContains('return 1;');
        $method->forgetReturn();
        $this->class()->save();
        $this->assertClassNotContains('return 1;');
        $this->class()->delete();
    }
}

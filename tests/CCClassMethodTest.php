<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Nodes\RowNode;

class CCClassMethodTest extends TestCase
{
    public function test_class_public_method()
    {
        $this->class();
        $this->class()->publicMethod('node');
        $this->class()->save();
        $this->assertClassContains('public function node()');
        $this->class()->forgetMethod('node');
        $this->class()->save();
        $this->assertClassNotContains('public function node()');
        $this->class()->delete();
    }

    public function test_class_protected_typed_method()
    {
        $this->class();
        $this->class()->protectedMethod([RowNode::class, 'node']);
        $this->class()->save();
        $this->assertClassContains('use Bfg\Comcode\Nodes\RowNode;');
        $this->assertClassContains('protected function node(): RowNode');
        $this->class()->forgetMethod('node');
        $this->class()->save()->standard();
        $this->assertClassNotContains('use Bfg\Comcode\Nodes\RowNode;');
        $this->assertClassNotContains('protected function node(): RowNode');
        $this->class()->delete();
    }
}

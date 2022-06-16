<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ClosureNode;
use Bfg\Comcode\Subjects\DocSubject;
use Bfg\Comcode\Traits\Conditionable;

class CCInterfaceTest extends TestCase
{
    public function test_interface_content()
    {
        $class = $this->interface();

        $class->save()->standard();

        $this->class = $class;

        $this->assertClassContains('<?php*');
        $this->assertClassContains('namespace Tests;');
        $this->assertClassContains('use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;');
        $this->assertClassContains('interface TestedInterface extends AlwaysLastNodeInterface');
        $class->delete();
    }
}

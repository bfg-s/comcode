<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ClosureNode;
use Bfg\Comcode\Subjects\DocSubject;
use Bfg\Comcode\Traits\Conditionable;

class CCEnumTest extends TestCase
{
    public function test_enum_content()
    {
        $class = $this->enum();

        $class->scalarType('int');

        $class->case('test1', 11123);

        $class->save()->standard();

        $this->class = $class;

        $this->assertClassContains('<?php*');
        $this->assertClassContains('namespace Tests;');
        $this->assertClassContains('enum MyEnum: int');
        $this->assertClassContains('case TEST1 = 11123;');

        $class->delete();
    }
}

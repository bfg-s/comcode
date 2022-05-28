<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\QueryNode;

class CCClassPropertyTest extends TestCase
{
    public function test_class_public_property()
    {
        $class = $this->class();
        $class->publicProperty('test1', 0);
        $class->publicProperty('test2', 1);
        $class->save();
        $this->assertClassContains('public $test1 = 0;');
        $this->assertClassContains('public $test2 = 1;');
        $class->forgetProperty('test1');
        $class->forgetProperty('test2');
        $class->save();
        $this->assertClassNotContains('public $test1 = 0;');
        $this->assertClassNotContains('public $test2 = 1;');
        $this->class()->delete();
    }

    public function test_class_protected_property()
    {
        $class = $this->class();
        $class->protectedProperty('test1', 0);
        $class->protectedProperty('test2', 1);
        $class->save();
        $this->assertClassContains('protected $test1 = 0;');
        $this->assertClassContains('protected $test2 = 1;');
        $class->forgetProperty('test1');
        $class->forgetProperty('test2');
        $class->save();
        $this->assertClassNotContains('protected $test1 = 0;');
        $this->assertClassNotContains('protected $test2 = 1;');
        $this->class()->delete();
    }

    public function test_class_protected_property_array()
    {
        $class = $this->class();
        $class->protectedProperty('test1', [1,2,3]);
        $class->save();
        $this->assertClassContains('protected $test1 = [1, 2, 3];');
        $class->forgetProperty('test1');
        $class->save();
        $this->assertClassNotContains('protected $test1 = [1, 2, 3];');
        $this->class()->delete();
    }

    public function test_class_protected_property_typed_array()
    {
        $class = $this->class();
        $class->protectedProperty(['array', 'test1'], [1,2,3]);
        $class->protectedProperty([QueryNode::class, 'test2'], [1,2,3]);
        $class->save();
        $this->assertClassContains('use Bfg\Comcode\QueryNode;');
        $this->assertClassContains('protected array $test1 = [1, 2, 3];');
        $this->assertClassContains('protected QueryNode $test2 = [1, 2, 3];');
        $class->forgetProperty('test1');
        $class->forgetProperty('test2');
        $class->save();
        $this->assertClassNotContains('use Bfg\Comcode\QueryNode;');
        $this->assertClassNotContains('protected array $test1 = [1, 2, 3];');
        $this->assertClassNotContains('protected QueryNode $test2 = [1, 2, 3];');
        $this->class()->delete();
    }
}

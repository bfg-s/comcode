<?php

namespace Bfg\Comcode\Tests;

class CCRawTest extends TestCase
{
    public function test_class_method_raw()
    {
        $method = $this->resetClass()->class()->publicMethod('nodeRaw');
        $method->row('test row 1')
            ->var('test')->assign(php()->raw('$a'));
        $method->row('test row 2')
            ->var('test')->assign(
                php()->real([
                    'a' => php()->rawForArray('$TEST')
                ])
            );
        $method->return()->var('test');
        $this->class()->save();

        $this->assertClassContains('public function nodeRaw()');
        $this->assertClassContains('// test row 1');
        $this->assertClassContains('$test = $a;');
        $this->assertClassContains('// test row 2');
        $this->assertClassContains('$test = [\'a\' => $TEST];');
        $this->assertClassContains('return $test;');
        //$this->class()->delete();
    }
}

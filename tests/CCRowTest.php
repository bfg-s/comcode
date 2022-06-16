<?php

namespace Bfg\Comcode\Tests;

class CCRowTest extends TestCase
{
    public function test_class_method_one_row()
    {
        $method = $this->resetClass()->class()->publicMethod('nodeRow');
        $method->row('test row 1')->var('test')->assign(php()->real(1));
        $method->return()->var('test');
        $this->class()->save();
        $this->assertClassContains('public function nodeRow()');
        $this->assertClassContains('// test row 1');
        $this->assertClassContains('$test = 1;');
        $this->assertClassContains('return $test;');
        $method->forgetRow('test row 1');
        $method->forgetReturn();
        $this->class()->save();
        $this->assertClassNotContains('return $test;');
        $this->assertClassNotContains('// test row 1');
        $this->assertClassNotContains('$test = 1;');
        $this->class()->delete();
    }

    public function test_class_method_couple_row()
    {
        $method = $this->resetClass()->class()->publicMethod('nodeRows');
        $method->row('test row 1')->var('test')->assign(php()->real(1));
        $method->row('test row 2')->var('test2')->assign(
            php('this')->nodeRows()->nodeRows
        );
        $method->row('test row 3')->var('test')->concat('test2');
        $method->return()->var('test');
        $this->class()->save();
        $this->assertClassContains('public function nodeRows()');
        $this->assertClassContains('// test row 1');
        $this->assertClassContains('$test = 1;');
        $this->assertClassContains('// test row 2');
        $this->assertClassContains('$test2 = $this->nodeRows()->nodeRows;');
        $this->assertClassContains('// test row 3');
        $this->assertClassContains('$test .= $test2;');
        $this->assertClassContains('return $test;');
        $method->forgetRow('test row 1');
        $method->forgetRow('test row 2');
        $method->forgetRow('test row 3');
        $method->forgetReturn();
        $this->class()->save();
        $this->assertClassNotContains('return $test;');
        $this->assertClassNotContains('// test row 1');
        $this->assertClassNotContains('$test = 1;');
        $this->assertClassNotContains('// test row 2');
        $this->assertClassNotContains('// test row 3');
        $this->assertClassNotContains('$test .= $test2;');
        $this->assertClassNotContains('$test2 = $this->nodeRows()->nodeRows;');
        $this->class()->delete();
    }
}

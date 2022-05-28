<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Nodes\ClosureNode;

class CCClosureTest extends TestCase
{
    public function test_class_method_one_row_with_closure()
    {
        $method = $this->class()->publicMethod('nodeRowClosure');
        $method->row('test row 1')
            ->var('test')
            ->assign('this')->testCall(function (ClosureNode $node) {
                $node->row('test row 1.1')->var('q')->assign('q')->first();
                $node->expectParams('q')
                    ->return()
                    ->var('q')
                    ->and(
                        fn (ClosureNode $node)
                        => $node->return('this')
                            ->myTestMethod()
                            ->andProperty
                    );
            });
        $method->return('test')->filter(fn (ClosureNode $node) => $node->return()->real(111));
        $this->class()->save();
        $this->assertClassContains('*// test row 1*$test = $this->testCall(function ($q) {*// test row 1.1*$q = $q->first();*return $q->and(fn () => $this->myTestMethod()->andProperty);*});*return $test->filter(fn () => 111);*');

        $method->forgetRow('test row 1');
        $method->forgetReturn();
        $this->class()->save();
        $this->assertClassNotContains('*// test row 1*$test = $this->testCall(function ($q) {*// test row 1.1*$q = $q->first();*return $q->and(fn () => $this->myTestMethod()->andProperty);*});*return $test->filter(fn () => 111);*');
        $this->class()->delete();
    }
}

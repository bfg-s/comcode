<?php

namespace Bfg\Comcode\Tests;

use ArrayAccess;
use Bfg\Comcode\Comcode;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ClassNode;
use Bfg\Comcode\SPrint;
use Bfg\Comcode\Traits\Conditionable;
use PhpParser\Node\Stmt\Property;
use Traversable;

class CCClassContentTest extends TestCase
{
    public function test_expr()
    {
        dd(
            Comcode::printStmt(
                php('this')
            )
        );
    }

    public function test_inline()
    {
        dd(
            php('this')->test2(1, 3)->first->property,
            Comcode::printStmt(
                php('this')->test2(
                    1, 3, php()->func('trim', php()->this()),
                )->first->property->meth(1,2,php('testr'), php()->func('trim', 1))
            )
        );
    }

    public function test_p()
    {
        dd(
            Comcode::printStmt(
                Node::const('public static', 'test', 1)
            )
        );

//        Node::property('public static', 'array:fields', [
//            'name', 'lastName', 'email', 'phone'
//        ]);
//        dd(
//            Comcode::printStmt(
//                Node::property('public static', [Bfg\Comcode\SPrint::class, 'table'])
//            ),
//            Comcode::printStmt(
//                Node::property('public static', 'array:info')
//            ),
//            Comcode::printStmt(
//                Node::property('public static', 'array:fields', [
//                    'name', 'lastName', 'email', 'phone'
//                ])
//            ),
//        );
    }
    public function test_qname()
    {
        //dd(SPrint::use_('test\\to'));
    }

    public function test_class_content()
    {
        $class = $this->class();

        $class->use(Comcode::class);
        $class->use(SPrint::class);
        $class->use(Property::class);
        $class->use(Traversable::class);
        $class->use(Traversable::class);
        $class->use(Conditionable::class);

        //dd($class->stmts);

        $class->extends('Comcode');
        $class->implement('Traversable');

        $class->trait('Conditionable');

        $class->forgetConst('test');
        $class->protectedConst('test2', 2.2);

        $class->publicProperty('array:table', ["aa"]);
        $class->privateProperty(['SPrint', 'users']);
        $class->privateProperty('test');

        $m1 = $class->protectedMethod('test1');

        $m1->return()->real('text1 - ');

        $test3 = $class->publicMethod('test3');

        $test3->return()->this()->test333->props->methidWithProps(1,2,3, 'string Property')->final;
        $test3->forgetReturn();

//        $test3->return()->this()->test2(1, 3)->first->property->test(
//            Node::var('this'),
//            Node::line('myVar')->gets()->sets()->property->andMethod()
//        );

        $method = $class->protectedMethod('test4');
//        $method->comment('// Hello text');
//        $return->comment('// Hello for return text');

        $method->row('exp')
            ->var('text')
            ->assign(
                php('this')
                    ->func('test1')
                    ->prop('zzz')
            );

        $method->row('exp2')
            ->var('text')
            ->plus(
                php()->real(2)
            );

        $method->row('exp33')
            ->var('text')
            ->minus(
                php()->real(33)
            );

        $method->row('exp23')
            ->var('text')
            ->plus(
                php()->real(2311)
            );

        $method->row('exp3')
            ->var('text')
            ->plus(
                php()->real(1223)
            );

        $method->row('exp311')
            ->var('text')
            ->minus(
                php()->real(311)
            );

        $method->row('exp11')
            ->var('text')
            ->plus(
                php()->real(400)
            );

        $method->row('exp5')
            ->var('text')
            ->plus(
                php()->real(100)
            );

        $return = $method->return();
        $return->func(
            'max',
            php('text')
        );

        dd(
            $class->save()
        );

        $class->dd(true);
    }

    public function test_has_constant()
    {
        $class = $this->class();
        $class->protectedConst('test', 2.2);

        $this->assertClassContains(
            'protected const TEST = 2.2;',
            $class
        );
    }

    public function test_has_property()
    {
        $class = $this->class();
        $class->privateProperty(['array', 'users'], []);

        $this->assertClassContains(
            'private array $users = [];',
            $class
        );
    }

    public function test_has_method()
    {
        $class = $this->class();
        $class->protectedMethod('test');

        $this->assertClassContains(
            "protected function test()",
            $class
        );
    }
}

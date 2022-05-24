<?php

namespace Bfg\Comcode\Tests;

use ArrayAccess;
use Bfg\Comcode\Comcode;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ClassNode;
use Bfg\Comcode\SPrint;
use PhpParser\Node\Stmt\Property;
use Traversable;

class CCClassContentTest extends TestCase
{
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

        //dd($class->stmts);

        $class->extends('Comcode');
        $class->implement('Traversable');

        $class->forgetConst('test');
        $class->protectedConst('test2', 2.2);

        $class->publicProperty('array:table', ["aa"]);
        $class->privateProperty(['SPrint', 'users']);
        $class->privateProperty('test');

        $class->protectedMethod('test1');

        $test3 = $class->protectedMethod('test3');

        $test3->return()->this()->test333->props->methidWithProps(1,2,3, 'string Property')->final;
        $test3->forgetReturn();

//        $test3->return()->this()->test2(1, 3)->first->property->test(
//            Node::var('this'),
//            Node::line('myVar')->gets()->sets()->property->andMethod()
//        );

        dd(
            $class->save()
        );



        $class->dd(true);

//        $class->body(function (ClassNode $node) {
//
//        });


        $content = $class->content();

        $this->assertTrue(
            str_contains($content, 'namespace Bfg\Comcode\Tests;')
        );
        $this->assertTrue(
            str_contains($content, SPrint::use_(Model::class))
        );
        $this->assertTrue(
            str_contains($content, SPrint::use_(Arrayable::class))
        );
        $this->assertTrue(
            str_contains($content, SPrint::use_(ArrayAccess::class))
        );
        $this->assertTrue(
            str_contains($content, 'class TestedClass extends Model implements Arrayable, ArrayAccess')
        );

        $class->fileSubject->delete();
    }
}

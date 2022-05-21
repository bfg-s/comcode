<?php

namespace Bfg\Comcode\Tests;

use ArrayAccess;
use Bfg\Comcode\Comcode;
use Bfg\Comcode\QStmt;
use Bfg\Comcode\QueryNodes\ClassQueryNode;
use Bfg\Comcode\SPrint;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class CCClassContentTest extends TestCase
{
    public function test_qname()
    {
        dd(QStmt::name('test\\to'));
    }

    public function test_class_content()
    {
        $class = $this->class();

        $class->use(Model::class);
        $class->use(Arrayable::class);
        $class->use(ArrayAccess::class);
        $class->use(ArrayAccess::class);

        //dd($class->stmts);

        $class->extends('Model');
        $class->implement('Arrayable');
        $class->implement('ArrayAccess');

//        $class->body(function (ClassQueryNode $node) {
//
//        });

        $class->save();

        $class->dd();

        $content = $class->fileGetContent();

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

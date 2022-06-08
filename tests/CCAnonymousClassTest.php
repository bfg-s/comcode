<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ClosureNode;
use Bfg\Comcode\Subjects\DocSubject;
use Bfg\Comcode\Traits\Conditionable;

class CCAnonymousClassTest extends TestCase
{
    public function test_anonymous_class_content_no_namespace()
    {
        $this->test_anonymous_class_content(true);
    }

    public function test_anonymous_class_content(bool $noNamespace = false)
    {
        if ($noNamespace) {
            $class = $this->anonymousClassNoNamespace();
        } else {
            $class = $this->anonymousClass();
        }

        $class->trait(Conditionable::class);

        $class->protectedConst('const1', 1);
        $class->protectedConst('const2', 1.1);
        $class->protectedConst('const3', false);
        $class->protectedConst('const3', ['success', 'error', 'warning', 'error', 'warning', 'error', 'warning']);

        $class->publicProperty('array:tables', ['users', 'migrations']);
        $class->protectedProperty('settings', false);
        $class->privateProperty([Comcode::class, 'engine']);

        $class->comment(function (DocSubject $docSubject) {
            $docSubject->description('Description of test class');
            $docSubject->tagMethod('static', 'get()');
        });

        $method1 = $class->protectedMethod('method1');

        $method1->return()->real('text');

        $method2 = $class->publicMethod('method2');

        $method2->comment(function (DocSubject $doc) {
            $doc->name('Method 2');
            $doc->description('And him Description');
            $doc->tagReturn('string');
        });

        $method2->expectParams(['wait', Comcode::defineValueNode(null), Node::class], 'attributes');

        $method2->row('test row 1')
            ->var('wait')->assign('this')->methodWithProps(1, function (ClosureNode $q) {
                $q->expectParams(['param1', 'default text', 'string']);
                $q->row('inner test row 1')->var('param1')->concat(php()->real(' and true text'));
                $q->row('inner test row 2')->var('param1')->concat(php()->real(' and false text'));
                $q->return('param1');
            })->method()->property->someMethod()->methodWithProps(function (ClosureNode $q) {
                $q->row('inner test row 1.1')->var('param1')->assign(php()->real(100));
                $q->row('inner test row 1.2')->var('param1')->plus(php()->real(200));

                $q->return('param1')->func('func', function (ClosureNode $node) {
                    $node->row('inner test row 1.2.1')->var('name')->concat(php()->real(' Xsaven'));
                    $node->expectParams('name')->return('name');
                });
            }, fn(ClosureNode $node) => $node->expectParams('name')->return()->var('name'));

        $method2->return('wait');

        $method3 = $class->protectedMethod('method3');
        $method3->comment('/**
                * Simple comment for method 3
                */');

        $method3->row('test row 1')
            ->var('text')
            ->assign('this')->property;

        $method3->row('test row 2')
            ->var('text')
            ->plus(
                php()->real(2)
            );

        $method3->row('test row 3')
            ->var('text')
            ->minus(
                php()->real(33)
            );

        $method3->row('test row 4')
            ->var('text')
            ->concat(
                php()->real(2311)
            );

        $method3->row('test row 5')
            ->var('text')
            ->plus(
                php()->real(1223)
            );

        $method3->row('test row 6')
            ->var('text')
            ->minus(
                php()->real(311)
            );

        $method3->row('test row 7')
            ->var('text')
            ->plus(
                php()->real(400)
            );

        $method3->row('test row 8')
            ->var('text')
            ->plus(
                php()->real(100)
            );

        $method3->return()->func('max', php('text'));

        $class->save()->standard();

        $this->class = $class;

        $this->assertClassContains('<?php*');
        if (! $noNamespace) {
            $this->assertClassContains('namespace Tests;');
        }
        $this->assertClassContains('use Bfg\Comcode\Comcode;');
        $this->assertClassContains('use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;');
        $this->assertClassContains('use Bfg\Comcode\Node;');
        $this->assertClassContains('use Bfg\Comcode\Traits\Conditionable;');
        $this->assertClassContains('return new class extends Comcode implements AlwaysLastNodeInterface {');
        $this->assertClassContains('use Conditionable;');
        $this->assertClassContains('protected const CONST1 = 1;');
        $this->assertClassContains('protected const CONST2 = 1.1;');
        $this->assertClassContains("*protected const CONST3 = [*'success',*'error',*'warning',*'error',*'warning',*'error',*'warning'*];*");
        $this->assertClassContains("public array \$tables = ['users', 'migrations'];");
        $this->assertClassContains('protected $settings = false;');
        $this->assertClassContains('private Comcode $engine;');
        $this->assertClassContains('And him Description.');
        $this->assertClassContains('    public function method2(Node $wait = null, $attributes)');
        $this->assertClassContains("*\$wait = \$this->methodWithProps(1, function (*string \$param1 = 'default text'*) {*");
        $this->assertClassContains("\$param1 .= ' and true text';");
        $this->assertClassContains("\$param1 .= ' and false text';");
        $this->assertClassContains('return $param1;');
        $this->assertClassContains('})->method()->property->someMethod()->methodWithProps(function () {');
        $this->assertClassContains('$param1 = 100;');
        $this->assertClassContains('$param1 += 200;');
        $this->assertClassContains('return $param1->func(function ($name) {');
        $this->assertClassContains("\$name .= ' Xsaven';");
        $this->assertClassContains('return $name;');
        $this->assertClassContains('}, fn ($name) => $name);');
        $this->assertClassContains('return $wait;');
        $this->assertClassContains('protected function method1()');
        $this->assertClassContains("return 'text';");
        $this->assertClassContains('Simple comment for method 3');
        $this->assertClassContains('protected function method3()');
        $this->assertClassContains('$text = $this->property;');
        $this->assertClassContains('$text += 2;');
        $this->assertClassContains('$text -= 33;');
        $this->assertClassContains('$text .= 2311;');
        $this->assertClassContains('$text += 1223;');
        $this->assertClassContains('$text -= 311;');
        $this->assertClassContains('$text += 400;');
        $this->assertClassContains('$text += 100;');
        $this->assertClassContains('return max($text);');

        $class->delete();
    }
}

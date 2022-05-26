<?php

namespace Bfg\Comcode\Tests;

use Bfg\Comcode\Node;

class CCNodesTest extends TestCase
{
    public function test_node_identifier()
    {
        $this->assertNodePrintLike(
            Node::identifier('test'),
            'test'
        );
    }

    public function test_node_var_id()
    {
        $this->assertNodePrintLike(
            Node::varId('test'),
            '$test'
        );
    }

    public function test_node_name()
    {
        $this->assertNodePrintLike(
            Node::name('test'),
            'test'
        );
    }

    public function test_node_namespace()
    {
        $this->assertNodePrintLike(
            Node::namespace('Test\\MyTestNamespace\\DeepNamespace'),
            "namespace Test\\MyTestNamespace\\DeepNamespace;\n"
        );
    }

    public function test_node_class()
    {
        $this->assertNodePrintLike(
            Node::class('Test'),
            "class Test\n{\n}"
        );
    }

    public function test_node_use()
    {
        $this->assertNodePrintLike(
            Node::use('Test\\MyTestNamespace\\DeepNamespace'),
            "use Test\MyTestNamespace\DeepNamespace;"
        );
    }

    public function test_node_property_simple()
    {
        $this->assertNodePrintLike(
            Node::property('public', 'test'),
            'public $test;'
        );
    }

    public function test_node_property()
    {
        $this->assertNodePrintLike(
            Node::property('public', 'test', []),
            'public $test = [];'
        );
    }

    public function test_node_public_method()
    {
        $this->assertNodePrintLike(
            Node::method('public', 'test'),
            "public function test()\n{\n}"
        );
    }

    public function test_node_protected_method()
    {
        $this->assertNodePrintLike(
            Node::method('protected', 'test'),
            "protected function test()\n{\n}"
        );
    }

    public function test_node_const()
    {
        $this->assertNodePrintLike(
            Node::const('public', 'test', null),
            "public const test = null;"
        );
    }

    public function test_node_protected_const()
    {
        $this->assertNodePrintLike(
            Node::const('protected', 'test', []),
            "protected const test = [];"
        );
    }

    public function test_node_return()
    {
        $this->assertNodePrintLike(
            Node::return(),
            "return;"
        );
    }

    public function test_node_variable()
    {
        $this->assertNodePrintLike(
            Node::var('test'),
            "\$test"
        );
    }

    public function test_node_args()
    {
        $this->assertNodePrintLike(
            Node::args(['test','test']),
            "'test'\n'test'"
        );
    }

    public function test_node_call_property()
    {
        $this->assertNodePrintLike(
            Node::callProperty(Node::var('test'), 'property'),
            "\$test->property"
        );
    }

    public function test_node_call_method()
    {
        $this->assertNodePrintLike(
            Node::callMethod(Node::var('test'), 'method'),
            "\$test->method()"
        );
    }

    public function test_node_call_function()
    {
        $this->assertNodePrintLike(
            Node::callFunction('trim', ' hello '),
            "trim(' hello ')"
        );
    }

    public function test_node_assign()
    {
        $this->assertNodePrintLike(
            Node::assign(Node::var('test'), php()->real()),
            "\$test = null"
        );
    }

    public function test_node_concat()
    {
        $this->assertNodePrintLike(
            Node::concat(Node::var('test'), Node::var('hello')),
            "\$test .= \$hello"
        );
    }

    public function test_node_plus()
    {
        $this->assertNodePrintLike(
            Node::plus(Node::var('test'), Node::var('hello')),
            "\$test += \$hello"
        );
    }

    public function test_node_minus()
    {
        $this->assertNodePrintLike(
            Node::minus(Node::var('test'), Node::var('hello')),
            "\$test -= \$hello"
        );
    }
}

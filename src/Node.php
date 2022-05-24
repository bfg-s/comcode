<?php

namespace Bfg\Comcode;

use PhpParser\Node\Arg;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\VarLikeIdentifier;

class Node
{
    public static function identifier(string $name): Identifier
    {
        return new Identifier($name);
    }

    public static function varId(string $name): VarLikeIdentifier
    {
        return new VarLikeIdentifier($name);
    }

    public static function name(string $name): Name
    {
        return new Name(explode("\\", $name));
    }

    public static function namespace(string $name): Namespace_
    {
        return new Namespace_(
            static::name($name)
        );
    }

    public static function class(string $name): Class_
    {
        return new Class_(
            static::name($name)
        );
    }

    public static function usesClass(UseUse $use): Use_
    {
        return new Use_([$use]);
    }

    public static function usesUseClass(string $namespace): UseUse
    {
        return new UseUse(
            static::name($namespace)
        );
    }

    public static function use(string $namespace): Use_
    {
        return new Use_([
            new UseUse(
                static::name($namespace)
            )
        ]);
    }

    /**
     * @param  string  $modifier
     * @param  string|array  $name
     * @param  string|null  $default
     * @return Property
     */
    public static function property(
        string $modifier,
        string|array $name,
        mixed $default = null,
    ): Property {
        $name = is_string($name) && str_contains($name, ':')
            ? explode(':', $name) : $name;
        return new Property(
            Comcode::detectPropertyModifier($modifier), [
            new PropertyProperty(
                new VarLikeIdentifier(is_array($name) ? $name[1] : $name),
                !is_null($default) ? Comcode::defineValueNode($default) : null
            )
        ], [], is_array($name) ? $name[0] : null
        );
    }

    /**
     * @param  string  $modifier
     * @param  string|array  $name
     * @return ClassMethod
     */
    public static function method(
        string $modifier,
        string|array $name,
    ): ClassMethod {
        $type = is_array($name) ? $name[0] : null;
        $name = is_array($name) ? $name[1] : $name;
        return new ClassMethod($name, [
            'flags' => Comcode::detectPropertyModifier($modifier),
            'returnType' => $type
        ]);
    }

    /**
     * @param  string|null  $modifier
     * @param  string|array  $name
     * @param  mixed  $value
     * @return ClassConst
     */
    public static function const(
        ?string $modifier,
        string|array $name,
        mixed $value,
    ): ClassConst {
        return new ClassConst([
            new Const_(
                static::identifier($name),
                Comcode::defineValueNode($value)
            )
        ], $modifier ? Comcode::detectPropertyModifier($modifier) : 0);
    }

    /**
     * @return Return_
     */
    public static function return(): Return_
    {
        return new Return_();
    }

    /**
     * @param  string|Expr  $var
     * @return Variable
     */
    public static function var(
        string|Expr $var
    ): Variable {
        return new Variable($var);
    }

    /**
     * @return Arg[]
     */
    public static function args(
        array $arguments
    ): array {
        foreach ($arguments as $key => $argument) {
            if (
                $argument instanceof PhpInlineTrap
            ) {
                $argument = $argument->node;
            }
            $arguments[$key]
                = Comcode::defineValueNode($argument);
        }
        return $arguments;
    }

    /**
     * @param  string|Expr  $expr
     * @param  string  $property
     * @return PropertyFetch
     */
    public static function callProperty(
        string|Expr $expr,
        string $property
    ): PropertyFetch {
        return new PropertyFetch(
            is_string($expr)
                ? Node::var($expr)
                : $expr,
            $property
        );
    }

    /**
     * @param  string|Expr  $expr
     * @param  string  $method
     * @param ...$arguments
     * @return MethodCall
     */
    public static function callMethod(
        string|Expr $expr,
        string $method,
        ...$arguments
    ): MethodCall {
        $node = new MethodCall(
            is_string($expr)
                ? Node::var($expr)
                : $expr,
            $method
        );
        $node->args
            = Node::args($arguments);
        return $node;
    }

    public static function callFunction(
        string $function,
        ...$arguments
    ): FuncCall {
        return new FuncCall(
            Node::name($function),
            Node::args($arguments)
        );
    }
}

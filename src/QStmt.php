<?php

namespace Bfg\Comcode;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

class QStmt
{
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
        return new Use_([new UseUse(
            static::name($namespace)
        )]);
    }
}

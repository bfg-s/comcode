<?php

namespace Bfg\Comcode;

class SPrint
{
    public static function use_(string $namespace): string
    {
        return Comcode::printStmt(
            Node::use($namespace)
        );
    }

    public function hasOne()
    {
        return (object) [
            'where' => fn($q) => $q
        ];
    }
}

<?php

namespace Bfg\Comcode;

class SPrint
{
    public static function use_(string $namespace): string
    {
        return Comcode::printStmt(QStmt::usesClass(
            QStmt::usesUseClass($namespace)
        ));
    }
}

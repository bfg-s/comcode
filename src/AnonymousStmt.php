<?php

namespace Bfg\Comcode;

use PhpParser\Node\Stmt;

class AnonymousStmt extends Stmt
{
    public function __construct(
        public array $stmts = [],
        array $attributes = []
    ) {
        parent::__construct($attributes);
    }

    public function getType(): string {
        return 'anonymous';
    }

    public function getSubNodeNames(): array {
        return [];
    }
}

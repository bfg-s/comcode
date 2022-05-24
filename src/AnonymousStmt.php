<?php

namespace Bfg\Comcode;

use PhpParser\NodeAbstract;

class AnonymousStmt extends NodeAbstract
{
    public function __construct(
        public array $nodes = [],
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

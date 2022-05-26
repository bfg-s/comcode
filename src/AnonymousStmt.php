<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\AnonymousInterface;
use PhpParser\NodeAbstract;

class AnonymousStmt extends NodeAbstract implements
    AnonymousInterface
{
    public function __construct(
        public array $nodes = [],
        array $attributes = []
    ) {
        parent::__construct($attributes);
    }

    public function getType(): string
    {
        return 'anonymous';
    }

    public function getSubNodeNames(): array
    {
        return [];
    }
}

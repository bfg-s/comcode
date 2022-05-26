<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\AnonymousInterface;
use PhpParser\Node\Expr;
use PhpParser\NodeAbstract;

class AnonymousExpr extends Expr implements
    AnonymousInterface
{
    public function __construct(
        public string|NodeAbstract|null $expr = null,
        array $attributes = []
    ) {
        parent::__construct($attributes);
    }

    public function getType(): string
    {
        return 'exanonymous';
    }

    public function getSubNodeNames(): array
    {
        return [];
    }
}

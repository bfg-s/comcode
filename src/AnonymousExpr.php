<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\AnonymousInterface;
use PhpParser\Node\Expr;
use PhpParser\NodeAbstract;

class AnonymousExpr extends Expr implements
    AnonymousInterface
{
    /**
     * @param  string|NodeAbstract|null  $expr
     * @param  array  $attributes
     */
    public function __construct(
        public string|NodeAbstract|null $expr = null,
        array $attributes = []
    ) {
        parent::__construct($attributes);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'exanonymous';
    }

    /**
     * @return array
     */
    public function getSubNodeNames(): array
    {
        return [];
    }
}

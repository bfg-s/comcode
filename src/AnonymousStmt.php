<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\AnonymousInterface;
use PhpParser\Node\Stmt;

class AnonymousStmt extends Stmt implements
    AnonymousInterface
{
    /**
     * @param  array  $nodes
     * @param  array  $attributes
     */
    public function __construct(
        public array $nodes = [],
        array $attributes = []
    ) {
        parent::__construct($attributes);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'anonymous';
    }

    /**
     * @return array
     */
    public function getSubNodeNames(): array
    {
        return [];
    }
}

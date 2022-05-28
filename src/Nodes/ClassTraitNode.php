<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeAbstract;

class ClassTraitNode extends QueryNode implements
    ClarificationNodeInterface, BirthNodeInterface
{
    /**
     * @var TraitUse|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var bool
     */
    public bool $prepend = true;

    /**
     * @var ClassNode|null
     */
    public ?QueryNode $parent;

    /**
     * @param  string  $namespace
     */
    public function __construct(
        public string $namespace,
    ) {
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return TraitUse::class;
    }

    /**
     * @return void
     */
    public function mounting(): void
    {
        $this->namespace
            = Comcode::useIfClass($this->namespace);
    }

    /**
     * @param  TraitUse|mixed  $stmt
     * @return bool
     */
    public function clarification(mixed $stmt): bool
    {
        if (
            (string) $stmt->traits[0] == $this->namespace
        ) {
            return true;
        }
        return false;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::trait($this->namespace);
    }
}

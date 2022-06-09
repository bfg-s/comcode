<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use PhpParser\Node\Name;
use PhpParser\NodeAbstract;

abstract class SimpleNamedNode extends QueryNode implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Name|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var ClassNode|null
     */
    public ?QueryNode $parent;

    /**
     * @param  string  $name
     */
    public function __construct(
        public string $name
    ) {
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Name::class;
    }

    /**
     * @return void
     */
    public function mounting(): void
    {
        $this->name
            = Comcode::useIfClass($this->name, $this->subject);
    }

    /**
     * @param  Name|mixed  $stmt
     * @param  string|int  $key
     * @return bool
     */
    public function clarification(mixed $stmt, string|int $key): bool
    {
        return (
                $stmt->__toString()
                == Node::name($this->name)->__toString()
            ) || (
                $stmt->__toString()
                == Comcode::classBasename($this->name)
            );
    }

    /**
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::name($this->name);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->node = Node::name($this->name);
    }
}

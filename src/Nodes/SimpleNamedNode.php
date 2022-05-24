<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
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
     * @var SubjectAbstract|ClassSubject
     */
    public SubjectAbstract|ClassSubject $subject;

    /**
     * @param  string  $name
     */
    public function __construct(
        public string $name
    ) {}

    /**
     * @param  Name|mixed  $stmt
     * @return bool
     */
    public function clarification(mixed $stmt): bool
    {
        if (
            $stmt->__toString()
            == Node::name($this->name)->__toString()
        ) {
            return true;
        }

        return false;
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

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Name::class;
    }
}

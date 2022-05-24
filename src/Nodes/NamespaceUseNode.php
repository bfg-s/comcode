<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeAbstract;

class NamespaceUseNode extends QueryNode implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Use_|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var QueryNode|NamespaceNode
     */
    public QueryNode|NamespaceNode $parent;

    /**
     * @var bool
     */
    public bool $prepend = true;

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
     * @param  Use_|mixed  $stmt
     * @return bool
     */
    public function clarification(mixed $stmt): bool
    {
        foreach ($stmt->uses as $use) {
            if (
                $use->name->__toString()
                == Node::name($this->name)->__toString()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::use($this->name);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->node->name = Node::name($this->name);
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Use_::class;
    }
}
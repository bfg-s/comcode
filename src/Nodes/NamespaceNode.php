<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeAbstract;

class NamespaceNode extends QueryNode implements
    ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Namespace_|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @param  string  $name
     */
    public function __construct(
        public string $name
    ) {
        $this->name = str_contains($this->name, '\\')
            ? Comcode::namespaceBasename($this->name)
            : $this->name;
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Namespace_::class;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::namespace($this->name);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->node->name = Node::name($this->name);
    }
}

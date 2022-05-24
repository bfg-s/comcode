<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Node;
use Bfg\Comcode\QueryNode;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeAbstract;

class ClassNode extends QueryNode
    implements ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Class_|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var SubjectAbstract|ClassSubject
     */
    public SubjectAbstract|ClassSubject $subject;

    /**
     * @var QueryNode|NamespaceNode
     */
    public QueryNode|NamespaceNode $parent;


    /**
     * @param  string  $name
     */
    public function __construct(
        public string $name
    ) {
        $this->name = str_contains($this->name, '\\')
            ? Comcode::classBasename($this->name)
            : $this->name;
    }

    public function extends(string $namespace): static
    {
        $this->apply(
            new ClassExtendsNode($namespace)
        );

        return $this;
    }

    public function implement(string $namespace): static
    {
        $this->apply(
            new ClassImplementNode($namespace)
        );

        return $this;
    }

    public function publicProperty(
        string|array $name,
        mixed $default = null
    ): static {

        $this->apply(
            new ClassPropertyNode('public', $name, $default)
        );

        return $this;
    }

    /**
     * NODE birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract
    {
        return Node::class($this->name);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->node->name->name = $this->name;
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Class_::class;
    }
}

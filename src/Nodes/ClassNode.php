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
     * @var ClassSubject
     */
    public SubjectAbstract $subject;

    /**
     * @param  string  $name
     */
    public function __construct(
        public ?string $name
    ) {
        if ($this->name) {
            $this->name = str_contains($this->name, '\\')
                ? Comcode::classBasename($this->name)
                : $this->name;
        }
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    public function nodeClass(): string
    {
        return Class_::class;
    }

    /**
     * @param  string  $namespace
     * @return void
     */
    public function use(
        string $namespace
    ): void {
        $this->subject
            ->use($namespace);
    }

    /**
     * @param  string  $namespace
     * @return $this
     */
    public function extends(
        string $namespace
    ): static {
        return $this->apply(
            new ClassExtendsNode($namespace)
        );
    }

    /**
     * @param  string  $namespace
     * @return $this
     */
    public function implement(
        string $namespace
    ): static {
        return $this->apply(
            new ClassImplementNode($namespace)
        );
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
}

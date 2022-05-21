<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\QStmt;
use Bfg\Comcode\QueryNodes\ClassQueryNode;
use Bfg\Comcode\QueryNodes\NamespaceQueryNode;

class ClassSubject extends SubjectAbstract
{
    /**
     * @var ClassQueryNode
     */
    protected ClassQueryNode $classNode;

    /**
     * @var NamespaceQueryNode
     */
    protected NamespaceQueryNode $namespaceNode;

    /**
     * @param  object|string  $class
     */
    public function __construct(
        public object|string $class,
    ) {}

    /**
     * @param  string  $namespace
     * @return $this
     */
    public function use(string $namespace): static
    {
        $this->namespaceNode->use($namespace);

        return $this;
    }

    /**
     * @param  string  $namespace
     * @return $this
     */
    public function extends(string $namespace): static
    {
        $this->classNode->extends($namespace);

        return $this;
    }

    /**
     * @param  string  $namespace
     * @return $this
     */
    public function implement(string $namespace): static
    {
        $this->classNode->implement($namespace);

        return $this;
    }

    /**
     * @param  callable  $callback
     * @return $this
     */
    public function body(callable $callback): static
    {
        call_user_func($callback, $this->classNode, $this);

        return $this;
    }

    /**
     * Discover individual environment
     * @return void
     */
    protected function discoverStmtEnvironment(): void
    {
        $this->namespaceNode = $this->apply(
            new NamespaceQueryNode($this->class)
        );

        $this->classNode = $this->namespaceNode->apply(
            new ClassQueryNode($this->class)
        );
    }
}

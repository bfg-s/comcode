<?php

namespace Bfg\Comcode\QueryNodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\QStmt;
use Bfg\Comcode\QueryNodeBuilder;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Namespace_;

class NamespaceQueryNode extends QueryNodeBuilder implements
    ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Namespace_|null
     */
    public ?Stmt $stmt = null;

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
     * @param  string  $namespace
     * @return $this
     */
    public function use(string $namespace): static
    {
        $this->apply(
            new UseQueryNode($namespace)
        );

        return $this;
    }

    /**
     * STMT birth method
     * @return Stmt
     */
    public function birth(): Stmt
    {
        return QStmt::namespace($this->name);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->stmt->name = QStmt::name($this->name);
    }

    /**
     * Get instance class of stmt type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Namespace_::class;
    }
}

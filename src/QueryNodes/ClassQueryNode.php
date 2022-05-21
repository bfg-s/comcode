<?php

namespace Bfg\Comcode\QueryNodes;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\QStmt;
use Bfg\Comcode\QueryNodeBuilder;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;

class ClassQueryNode extends QueryNodeBuilder
    implements ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Class_|null
     */
    public ?Stmt $stmt = null;

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
        $this->stmt->extends = QStmt::name(
            $namespace
        );

        return $this;
    }

    public function implement(string $namespace)
    {
        $exists = false;
        $newName = QStmt::name($namespace);
        foreach ($this->stmt->implements as $implement) {
            if ($implement->parts == $newName->parts) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->stmt->implements[] = $newName;
        }
    }

    /**
     * STMT birth method
     * @return Stmt
     */
    public function birth(): Stmt
    {
        return QStmt::class($this->name);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->stmt->name->name = $this->name;
    }

    /**
     * Get instance class of stmt type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Class_::class;
    }
}

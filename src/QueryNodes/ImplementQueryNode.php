<?php

namespace Bfg\Comcode\QueryNodes;

use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\QStmt;
use Bfg\Comcode\QueryNodeBuilder;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Use_;

class ImplementQueryNode extends QueryNodeBuilder implements
    ClarificationNodeInterface, ReconstructionNodeInterface, BirthNodeInterface
{
    /**
     * @var Name|null
     */
    public ?Stmt $stmt = null;

    /**
     * @var bool
     */
    public bool $prepend = true;

    /**
     * @var SubjectAbstract|ClassSubject
     */
    public SubjectAbstract|ClassSubject $subjectAbstract;

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
                == QStmt::name($this->name)->__toString()
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * STMT birth method
     * @return Stmt
     */
    public function birth(): Stmt
    {
        return QStmt::use($this->name);
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
        return Use_::class;
    }
}

<?php

namespace Bfg\Comcode\QueryNodes;

use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\QStmt;
use Bfg\Comcode\QueryNodeBuilder;
use Bfg\Comcode\Subjects\ClassSubject;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Use_;

class UseQueryNode extends QueryNodeBuilder implements
    ClarificationNodeInterface, BirthNodeInterface
{
    /**
     * @var Use_|null
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
            if ($use->name->parts == QStmt::name($this->name)->parts) {
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
     * Get instance class of stmt type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return Use_::class;
    }
}

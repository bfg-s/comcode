<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\AnonymousStmt;
use Bfg\Comcode\Comcode;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\PrettyPrinter;
use Bfg\Comcode\QueryNodeBuilder;
use Bfg\Comcode\Query;
use Bfg\Comcode\Traits\Conditionable;
use JetBrains\PhpStorm\NoReturn;
use PhpParser\Node\Stmt;

abstract class SubjectAbstract implements \Stringable
{
    use Conditionable;

    public array $stmts = [];

    public Stmt $stmt;

    public FileSubject $fileSubject;

    public function setUp(FileSubject $fileSubject): static
    {
        $this->fileSubject = $fileSubject;
        $this->stmts = Comcode::parsPhpFile($this->fileSubject->file);
        $this->stmt = Comcode::anonymousStmt($this->stmts);
        $this->discoverStmtEnvironment();
        return $this;
    }

    /**
     * Create new query node content
     * @template QUERY_NODE
     * @param  QUERY_NODE|QueryNodeBuilder  $nodeClass
     * @return QUERY_NODE
     */
    public function apply(
        QueryNodeBuilder $nodeClass
    ): QueryNodeBuilder {

        $nodeClass->subjectAbstract = $this;

        $query = Query::new($this->stmts)->isA(
            $nodeClass::nodeClass()
        )->filter(
            $nodeClass instanceof ClarificationNodeInterface
                ? [$nodeClass, 'clarification'] : null
        );

        $key = $query->firstKey();

        $nodeClass->stmt = $query->first();

        $nodeClass->isMatch()
            ? $nodeClass instanceof ReconstructionNodeInterface && $nodeClass->reconstruction()
            : $nodeClass instanceof BirthNodeInterface && $nodeClass->stmt = $nodeClass->birth();

        if (is_int($key)) {
            $this->stmts[$key] = $nodeClass->stmt;
        } else {
            $this->stmts = [$nodeClass->stmt];
        }

        return $nodeClass;
    }

    /**
     * Save nodes to file
     * @return bool|int
     */
    public function save(): bool|int
    {
        return file_put_contents(
            $this->fileSubject->file,
            (string) $this
        );
    }

    /**
     * @return string
     */
    public function fileGetContent(): string
    {
        return file_get_contents(
            $this->fileSubject->file
        ) ?? '';
    }

    /**
     * Discover individual stmt environment
     * @return void
     */
    abstract protected function discoverStmtEnvironment(): void;

    /**
     * @param  mixed  ...$params
     * @return $this
     */
    public static function create(...$params): static
    {
        return new static(...$params);
    }

    /**
     * Create stmt list from collection items
     * @return array
     */
    public function toStmt(): array
    {
        return Comcode::undressNodes($this->stmts);
    }

    #[NoReturn]
    public function dd()
    {
        dd($this->__toString());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (new PrettyPrinter)
            ->prettyPrintFile(
                $this->toStmt()
            );
    }
}

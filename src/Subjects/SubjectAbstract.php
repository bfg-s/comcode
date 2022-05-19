<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\Exceptions\QueryNodeError;
use Bfg\Comcode\PrettyPrinter;
use Bfg\Comcode\QueryNodeBuilder;
use Bfg\Comcode\Traits\Conditionable;
use JetBrains\PhpStorm\NoReturn;
use PhpParser\Node\Stmt;

abstract class SubjectAbstract implements \Stringable
{
    use Conditionable;

    public array $stmts = [];

    public FileSubject $fileSubject;

    public function setUp(FileSubject $fileSubject): static
    {
        $this->fileSubject = $fileSubject;
        $this->stmts = Comcode::parsPhpFile($this->fileSubject->file);
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
        return Comcode::createQueryContent(
            $this->stmts,
            $nodeClass,
            $this
        );
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

<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\Node\Stmt;

abstract class QueryNodeBuilder
{
    /**
     * @var Stmt|null
     */
    public ?Stmt $stmt = null;

    /**
     * @var bool
     */
    public bool $prepend = false;

    /**
     * @var SubjectAbstract
     */
    public SubjectAbstract $subjectAbstract;

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
            $this->stmt,
            $nodeClass,
            $this->subjectAbstract
        );
    }

    /**
     * @return bool
     */
    public function isMatch(): bool
    {
        return $this->stmt && is_a($this->stmt, static::nodeClass());
    }

    /**
     * @param  Stmt|null  $stmt
     * @return QueryNodeBuilder
     */
    public function setUp(Stmt $stmt = null): static
    {
        $this->stmt = $stmt ?: Comcode::anonymousStmt();

        $this->mount();

        return $this;
    }

    /**
     * The mount function for checking the stmt class
     * @return void
     */
    protected function mount(): void
    {
        $this->isMatch()
            ? $this instanceof ReconstructionNodeInterface && $this->reconstruction()
            : $this instanceof BirthNodeInterface && $this->stmt = $this->birth();
    }

    /**
     * Finder self abstract
     * @param  QuerySearchEngine  $query
     * @param  SubjectAbstract  $subjectAbstract
     * @return static
     */
    public function finder(QuerySearchEngine $query, SubjectAbstract $subjectAbstract): static
    {
        $this->subjectAbstract = $subjectAbstract;

        return $query->isA(
            static::nodeClass(),
            $this instanceof ClarificationNodeInterface
                ? [$this, 'clarification'] : null
        )->first($this, $this->prepend);
    }

    /**
     * Get instance class of stmt type
     * @return <class-string>
     */
    abstract public static function nodeClass(): string;

    /**
     * @return bool
     */
    public function isAnonymous(): bool
    {
        return $this->stmt?->getType() === 'anonymous';
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->stmt, $name], $arguments);
    }

    /**
     * @param  string  $name
     * @return null
     */
    public function __get(string $name)
    {
        return $this->stmt?->{$name};
    }

    public function __toString(): string
    {
        return (string) $this->stmt;
    }

    public static function new(...$arguments): static
    {
        return new static(...$arguments);
    }
}

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
     * @var string
     */
    public string $store = 'stmts';

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

        $store = $nodeClass->store;

        $nodeClass->subjectAbstract = $this->subjectAbstract;

        $query = Query::new((array) $this->stmt?->{$store})
            ->isA($nodeClass::nodeClass())
            ->filter(
                $nodeClass instanceof ClarificationNodeInterface
                    ? [$nodeClass, 'clarification'] : null
            );

        $key = $query->firstKey();

        $nodeClass->stmt = $query->first();

        $nodeClass->isMatch()
            ? $nodeClass instanceof ReconstructionNodeInterface && $nodeClass->reconstruction()
            : $nodeClass instanceof BirthNodeInterface && $nodeClass->stmt = $nodeClass->birth();

        if (property_exists($this->stmt, $store)) {
            if (is_array($this->stmt->{$store})) {
                if (is_int($key)) {
                    $this->stmt->{$store}[$key] = $nodeClass->stmt;
                } else {
                    if ($nodeClass->prepend) {
                        array_unshift($this->stmt->{$store}, $nodeClass->stmt);
                    } else {
                        $this->stmt->{$store}[] = $nodeClass->stmt;
                    }
                }
            } else {
                $this->stmt->{$store} = $nodeClass->stmt;
            }
        }

        return $nodeClass;
    }

    /**
     * @return bool
     */
    public function isMatch(): bool
    {
        return $this->stmt && is_a($this->stmt, static::nodeClass());
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

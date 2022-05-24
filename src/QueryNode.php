<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Subjects\SubjectAbstract;
use PhpParser\NodeAbstract;

abstract class QueryNode
{
    /**
     * @var NodeAbstract|null
     */
    public ?NodeAbstract $node = null;

    /**
     * @var QueryNode
     */
    public QueryNode $parent;

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
    public SubjectAbstract $subject;

    /**
     * Create new query node content
     * @template QUERY_NODE
     * @param  QUERY_NODE|QueryNode  $nodeClass
     * @return QUERY_NODE
     */
    public function apply(
        QueryNode $nodeClass
    ): QueryNode {

        $nodeClass->parent = $this;

        $nodeClass->subject = $this->subject;

        $store = $nodeClass->store;

        $query = Query::new($this->node?->{$store})
            ->isA($nodeClass::nodeClass())
            ->filter(
                $nodeClass instanceof ClarificationNodeInterface
                    ? [$nodeClass, 'clarification'] : null
            );

        $key = $query->firstKey();

        $nodeClass->node = $query->first();

        $nodeClass->isMatch()
            ? $nodeClass instanceof ReconstructionNodeInterface && $nodeClass->reconstruction()
            : $nodeClass instanceof BirthNodeInterface && $nodeClass->node = $nodeClass->birth();

        if (property_exists($this->node, $store)) {
            if (is_array($this->node->{$store})) {
                if (is_int($key)) {
                    $this->node->{$store}[$key] = $nodeClass->node;
                } else {
                    if ($nodeClass->prepend) {
                        array_unshift($this->node->{$store}, $nodeClass->node);
                    } else {
                        $this->node->{$store}[] = $nodeClass->node;
                    }
                }
            } else {
                $this->node->{$store} = $nodeClass->node;
            }
        }

        $nodeClass->mounted();

        return $nodeClass;
    }

    public function forget(
        QueryNode $nodeClass
    ): bool {
        $store = $nodeClass->store;
        $query = Query::new((array) $this->node->{$store})
            ->isA($nodeClass::nodeClass())
            ->filter(
                $nodeClass instanceof ClarificationNodeInterface
                    ? [$nodeClass, 'clarification'] : null
            );

        $key = $query->firstKey();

        if (property_exists($this->node, $store)) {
            if (is_array($this->node->{$store})) {
                if (is_int($key)) {
                    $arr = $this->node->{$store};
                    unset($arr[$key]);
                    $this->node->{$store} = array_values($arr);
                    return true;
                }
            } else {
                $this->node->{$store} = null;
                return true;
            }
        }
        return false;
    }

    public function mounted(): void
    {

    }

    /**
     * @return bool
     */
    public function isMatch(): bool
    {
        return $this->node && is_a($this->node, static::nodeClass());
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    abstract public static function nodeClass(): string;

    /**
     * @param  string  $name
     * @return null
     */
    public function __get(string $name)
    {
        return $this->node?->{$name};
    }

    public function __toString(): string
    {
        return (string) $this->node;
    }

    public static function new(...$arguments): static
    {
        return new static(...$arguments);
    }

    public static function modified(): bool
    {
        return false;
    }
}

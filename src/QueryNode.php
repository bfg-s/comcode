<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\AlwaysLastNodeInterface;
use Bfg\Comcode\Interfaces\AnonymousInterface;
use Bfg\Comcode\Interfaces\BirthNodeInterface;
use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Interfaces\ReconstructionNodeInterface;
use Bfg\Comcode\Nodes\RowNode;
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
    public ?QueryNode $parent = null;

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

    public int $rowCount = 0;

    public static function modified(): bool
    {
        return false;
    }

    /**
     * @param  string  $name
     * @return RowNode
     */
    public function row(
        string $name
    ): RowNode {
        $this->rowCount++;
        return $this->apply(
            new RowNode($name)
        );
    }

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

        $store = $nodeClass->store !== 'stmts'
            ? $nodeClass->store
            : $this->store;

        $query = Query::new($this->node->{$store})
            ->unless(
                $nodeClass instanceof AnonymousInterface,
                fn(Query $query) => $query
                    ->isA($nodeClass::nodeClass())
            )->when(
                $nodeClass instanceof ClarificationNodeInterface,
                fn(Query $query) => $query
                    ->filter([$nodeClass, 'clarification'])
            );

        $key = $query->firstKey();

        $nodeClass->node = $query->first();

        $nodeClass->isMatch()
            ? $nodeClass instanceof ReconstructionNodeInterface && $nodeClass->reconstruction()
            : $nodeClass instanceof BirthNodeInterface && $nodeClass->node = $nodeClass->birth();

        if (property_exists($this->node, $store)) {
            if (is_array($this->node->{$store})) {
                if (is_int($key)) {
                    if ($nodeClass instanceof AlwaysLastNodeInterface) {
                        $this->node->{$store}[] = $nodeClass->node;
                        unset($this->node->{$store}[$key]);
                    } else {
                        $this->node->{$store}[$key] = $nodeClass->node;
                    }
                } else {

                    if (
                        $nodeClass instanceof RowNode
                        && $this->rowCount
                    ) {
                        $this->node->{$store} = $this->moveBefore(
                            $this->node->{$store}, $this->rowCount-1, $nodeClass->node
                        );
                    } else {
                        if ($nodeClass->prepend) {
                            array_unshift($this->node->{$store}, $nodeClass->node);
                        } else {
                            $this->node->{$store}[] = $nodeClass->node;
                        }
                    }
                }
            } else {
                $this->node->{$store} = $nodeClass->node;
            }
        }

        $nodeClass->mounted();

        return $nodeClass;
    }

    public static function new(...$arguments): static
    {
        return new static(...$arguments);
    }

    /**
     * Get instance class of node type
     * @return <class-string>
     */
    abstract public static function nodeClass(): string;

    /**
     * @return bool
     */
    public function isMatch(): bool
    {
        return $this->node && is_a($this->node, static::nodeClass());
    }

    public function mounted(): void
    {
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

    public function comment(
        string $text
    ): static {
        if ($this->node?->hasAttribute('comments')) {
            $comments = $this->node->getComments();
        }

        $this->node?->setDocComment(
            Node::doc($text)
        );

        return $this;
    }

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

    /**
     * Change the order of an associated array, moving by key before another
     *
     * @param  array  $items
     * @param  int  $key
     * @param  mixed  $value
     * @return array
     */
    protected function moveBefore(
        array $items,
        int $key,
        mixed $value
    ): array {
        $key = max($key, 0);
        $count = count($items);
        $lastKey = array_key_last($items);
        if (!$count) {
            $items[] = $value;
            return $items;
        }
        if (!is_null($lastKey) && $lastKey < $key) {
            $items[$key] = $value;
            return $items;
        }
        $result = [];
        $iterations = 0;
        foreach ($items as $itemKey => $item) {
            if (
                $iterations >= $key
                && $itemKey <= $key
                && $value
            ) {
                $result[] = $value;
                $value = null;
            }
            $result[] = $item;
            $iterations++;
        }
        return $result;
    }
}

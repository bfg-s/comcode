<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Interfaces\ClarificationNodeInterface;
use Bfg\Comcode\Traits\Conditionable;
use Bfg\Comcode\Traits\EngineHelpersTrait;
use PhpParser\NodeAbstract;

class Query
{
    use EngineHelpersTrait;
    use Conditionable;

    /**
     * @param  array  $items
     */
    public function __construct(
        public array $items = []
    ) {
    }

    /**
     * @param  NodeAbstract  $node
     * @param  QueryNode  $queryNode
     * @return Query
     */
    public static function find(
        NodeAbstract $node,
        QueryNode $queryNode
    ): Query {
        $queryNode->mounting();
        return static::new($node->{$queryNode->store})->isA($queryNode->nodeClass())
            ->filter(
                $queryNode instanceof ClarificationNodeInterface
                    ? [$queryNode, 'clarification'] : null
            );
    }

    public function dd()
    {
        dd($this->items);
    }

    /**
     * @param  callable|null  $callback
     * @return $this
     */
    public function filter(callable $callback = null): static
    {
        if ($callback) {
            $result = [];

            foreach ($this->items as $key => $item) {
                if (call_user_func($callback, $item, $key)) {
                    $result[$key] = $item;
                }
            }

            $this->items = $result;
        }

        return $this;
    }

    /**
     * @param  string  $class
     * @return $this
     */
    public function isA(string $class): static
    {
        return $this->filter(
            fn($stmt) => is_a($stmt, $class)
        );
    }

    /**
     * @param  mixed  $item
     * @return static
     */
    public static function new(mixed $item = []): static
    {
        return new static(is_array($item) ? $item : [$item]);
    }

    /**
     * @return NodeAbstract
     */
    public function first(): NodeAbstract
    {
        return $this->items[$this->firstKey()]
            ?? Comcode::anonymousStmt();
    }

    /**
     * @return int|string|void|null
     */
    public function firstKey()
    {
        return array_key_first($this->items);
    }
}

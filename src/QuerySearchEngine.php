<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Traits\Conditionable;
use PhpParser\Node\Stmt;
use Bfg\Comcode\Traits\EngineHelpersTrait;

class QuerySearchEngine
{
    use EngineHelpersTrait;
    use Conditionable;

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @var bool
     */
    protected bool $content = false;

    /**
     * @param  Stmt  $stmt
     */
    public function __construct(
        public Stmt $stmt
    ) {
        $this->items = $this->stmt->stmts ?? [];
    }

    /**
     * @param  string  $class
     * @param  callable|null  $clarificationCallback
     * @return $this
     */
    public function isA(string $class, ?callable $clarificationCallback = null): static
    {
        return $this
            ->unless(
                $clarificationCallback,
                fn (QuerySearchEngine $query)
                => $query->filter(
                    fn ($stmt) => is_a($stmt, $class)
                )
            )->when(
                $clarificationCallback,
                fn (QuerySearchEngine $query)
                => $query->filter(
                    fn ($stmt) => is_a($stmt, $class)
                        ? $clarificationCallback
                        : fn () => false
                )
            );
    }

    /**
     * @param  callable|null  $callback
     * @return $this
     */
    public function filter(callable $callback = null): static
    {
        $result = [];

        if (property_exists($this->stmt, 'stmts')) {

            foreach ($this->stmt->stmts as $key => $item) {

                if (
                    $callback
                    && call_user_func(
                        $callback,
                        $item instanceof QueryNodeBuilder
                            ? $item->stmt
                            : $item,
                        $key
                    )
                ) {
                    $result[$key] = $item;
                }
            }
        }

        $this->items = $result;

        return $this;
    }

    /**
     * @template QueryNodeWrapper
     * @param  QueryNodeWrapper|QueryNodeBuilder  $wrap
     * @return QueryNodeWrapper
     */
    public function first(QueryNodeBuilder $wrap, bool $prepend = false)
    {
        $firstKey = array_key_first($this->items);

        $first = is_null($firstKey) ? null : $this->items[$firstKey];

        if ($first) {

            if (
                $first instanceof QueryNodeBuilder
                && $first->stmt
            ) {
                $first = $first->stmt;
            }

            return $wrap->setUp($first);
        }

        if (property_exists($this->stmt, 'stmts')) {

            if ($prepend) {
                array_unshift($this->stmt->stmts, $wrap);
                array_unshift($this->items, $wrap);
            } else {

                $this->stmt->stmts[]
                    = $this->items[]
                    = $wrap;
            }
        }

        return $wrap->setUp();
    }

    /**
     * @param  QueryNodeBuilder  $wrap
     * @return QueryNodeBuilder|mixed|void
     */
    public function last(QueryNodeBuilder $wrap)
    {
        $lastKey = array_key_last($this->items);

        if ($lastKey) {

            $this->items = [$this->items[$lastKey]];

            return $this->first($wrap);
        }
    }
}

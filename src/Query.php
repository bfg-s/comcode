<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Traits\Conditionable;
use Bfg\Comcode\Traits\EngineHelpersTrait;
use PhpParser\NodeAbstract;

class Query
{
    use EngineHelpersTrait;
    use Conditionable;

    /**
     * @var bool
     */
    protected bool $content = false;

    /**
     * @param  array  $items
     */
    public function __construct(
        public array $items = []
    ) {
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

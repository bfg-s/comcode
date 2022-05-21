<?php

namespace Bfg\Comcode;

use Bfg\Comcode\Traits\Conditionable;
use PhpParser\Node\Stmt;
use Bfg\Comcode\Traits\EngineHelpersTrait;

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
    ) {}

    /**
     * @param  string  $class
     * @return $this
     */
    public function isA(string $class): static
    {
        return $this->filter(
            fn ($stmt) => is_a($stmt, $class)
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
     * @return Stmt
     */
    public function first(): Stmt
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

    /**
     * @param  array  $item
     * @return static
     */
    public static function new(array $item = []): static
    {
        return new static($item);
    }
}

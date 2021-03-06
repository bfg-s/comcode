<?php

namespace Bfg\Comcode;

class HigherOrderWhenProxy
{
    /**
     * The target being conditionally operated on.
     *
     * @var mixed
     */
    protected mixed $target;

    /**
     * The condition for proxying.
     *
     * @var bool
     */
    protected bool $condition;

    /**
     * Create a new proxy instance.
     *
     * @param  mixed  $target
     * @param  bool  $condition
     * @return void
     */
    public function __construct(mixed $target, bool $condition)
    {
        $this->target = $target;
        $this->condition = $condition;
    }

    /**
     * Proxy accessing an attribute onto the target.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->condition
            ? $this->target->{$key}
            : $this->target;
    }

    /**
     * Proxy a method call on the target.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->condition
            ? $this->target->{$method}(...$parameters)
            : $this->target;
    }
}

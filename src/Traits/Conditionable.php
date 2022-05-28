<?php

namespace Bfg\Comcode\Traits;

use Bfg\Comcode\HigherOrderWhenProxy;
use Closure;

trait Conditionable
{
    /**
     * Apply the callback if the given "value" is (or resolves to) falsy.
     *
     * @template TUnlessParameter
     * @template TUnlessReturnType
     *
     * @param  (\Closure($this): TUnlessParameter)|TUnlessParameter  $value
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return $this|TUnlessReturnType
     */
    public function unless($value, callable $callback = null, callable $default = null)
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (!$callback) {
            return new HigherOrderWhenProxy($this, !$value);
        }

        if (!$value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }

    /**
     * @param  callable|null  $callback
     * @return $this
     */
    public function and(callable $callback = null): static
    {
        return $this->when(true, $callback);
    }

    /**
     * Apply the callback if the given "value" is (or resolves to) truthy.
     *
     * @template TWhenParameter
     * @template TWhenReturnType
     *
     * @param  (\Closure($this): TWhenParameter)|TWhenParameter  $value
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return $this|TWhenReturnType
     */
    public function when($value, callable $callback = null, callable $default = null)
    {
        $value = $value instanceof Closure ? $value($this) : $value;

        if (!$callback) {
            return new HigherOrderWhenProxy($this, $value);
        }

        if ($value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }

        return $this;
    }
}

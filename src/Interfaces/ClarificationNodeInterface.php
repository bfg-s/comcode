<?php

namespace Bfg\Comcode\Interfaces;

interface ClarificationNodeInterface
{
    /**
     * @param  mixed  $stmt
     * @param  string|int  $key
     * @return bool
     */
    public function clarification(mixed $stmt, string|int $key): bool;
}

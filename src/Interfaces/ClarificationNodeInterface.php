<?php

namespace Bfg\Comcode\Interfaces;

interface ClarificationNodeInterface
{
    /**
     * @param  mixed  $stmt
     * @return bool
     */
    public function clarification(mixed $stmt): bool;
}

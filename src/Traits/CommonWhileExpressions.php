<?php

namespace Bfg\Comcode\Traits;

use Bfg\Comcode\Nodes\ReturnNode;

trait CommonWhileExpressions
{
    public function return()
    {
        return $this->apply(
            new ReturnNode()
        );
    }

    public function forgetReturn(): bool
    {
        return $this->forget(
            new ReturnNode()
        );
    }
}

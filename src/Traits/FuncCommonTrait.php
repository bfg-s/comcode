<?php

namespace Bfg\Comcode\Traits;

use Bfg\Comcode\InlineTrap;
use Bfg\Comcode\Node;
use Bfg\Comcode\Nodes\ReturnNode;
use PhpParser\Node\Expr;

trait FuncCommonTrait
{
    /**
     * @param ...$parameters
     * @return $this
     */
    public function expectParams(
        ...$parameters
    ): static {
        foreach ($parameters as $key => $parameter) {
            $this->node->params[$key]
                = is_array($parameter)
                ? Node::param($this->subject, ...$parameter)
                : Node::param($this->subject, $parameter);
        }

        return $this;
    }

    /**
     * @param  string|Expr|null  $node
     * @return ReturnNode|InlineTrap
     */
    public function return(
        string|Expr|null $node = null
    ): ReturnNode|InlineTrap {
        $result = $this->apply(
            new ReturnNode()
        );
        if ($node) {
            return $result->var($node);
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function forgetReturn(): bool
    {
        return $this->forget(
            new ReturnNode()
        );
    }

    public function existsReturn(): bool
    {
        return $this->exists(
            new ReturnNode()
        );
    }

    /**
     * @return bool
     */
    public function notExistsReturn(): bool {
        return ! $this->existsReturn();
    }
}

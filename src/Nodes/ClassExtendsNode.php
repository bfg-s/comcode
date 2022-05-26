<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\QueryNode;

class ClassExtendsNode extends SimpleNamedNode
{
    /**
     * @var string
     */
    public string $store = "extends";
}

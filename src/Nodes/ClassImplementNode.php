<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\QueryNode;

class ClassImplementNode extends SimpleNamedNode
{
    /**
     * @var string
     */
    public string $store = "implements";

    /**
     * @var QueryNode|ClassNode
     */
    public QueryNode|ClassNode $parent;
}

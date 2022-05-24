<?php

namespace Bfg\Comcode\Nodes;

use Bfg\Comcode\QueryNode;

class ClassExtendsNode extends SimpleNamedNode
{
    /**
     * @var string
     */
    public string $store = "extends";

    /**
     * @var QueryNode|ClassNode
     */
    public QueryNode|ClassNode $parent;
}

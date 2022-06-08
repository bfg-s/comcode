<?php

namespace Bfg\Comcode\Nodes;

class AnonymousClassNode extends ClassNode
{
    /**
     * @var string
     */
    public string $store = "class";

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * STMT reconstruction method
     * @return void
     */
    public function reconstruction(): void
    {
        $this->node->name = null;
    }
}

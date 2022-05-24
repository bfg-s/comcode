<?php

namespace Bfg\Comcode\Interfaces;

use PhpParser\NodeAbstract;

interface BirthNodeInterface
{
    /**
     * STMT birth method
     * @return NodeAbstract
     */
    public function birth(): NodeAbstract;
}

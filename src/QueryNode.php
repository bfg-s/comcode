<?php

namespace Bfg\Comcode;

class QueryNode extends QueryNodeBuilder
{
    /**
     * The mount function for checking the stmt class
     * @return void
     */
    protected function mount(): void
    {

    }

    /**
     * Get instance class of stmt type
     * @return <class-string>
     */
    public static function nodeClass(): string
    {
        return AnonymousStmt::class;
    }
}

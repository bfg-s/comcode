<?php

namespace Bfg\Comcode\Subjects;

use Closure;

class FunctionSubject extends SubjectAbstract
{
    /**
     * @param  Closure|string  $function
     * @param  FileSubject  $fileSubject
     */
    public function __construct(
        public Closure|string $function,
        protected FileSubject $fileSubject,
    ) {
        parent::__construct();
    }

    /**
     * Discover individual environment
     * @return void
     */
    protected function discoverStmtEnvironment(): void
    {
        //
    }
}

<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\FileParser;
use Closure;

class FileSubject extends SubjectAbstract
{
    /**
     * @param  string  $file
     */
    public function __construct(
       public string $file,
    ) {
        parent::__construct();
    }

    /**
     * CHILDHOOD FUNCTION
     * @param  object|string|null  $class
     * @return ClassSubject
     */
    public function class(object|string $class = null): ClassSubject
    {
        return new ClassSubject(
            $class ?: (new FileParser)->getClassFullNameFromFile($this->file), $this
        );
    }

    /**
     * CHILDHOOD FUNCTIONS
     * @param  Closure|string  $function
     * @return FunctionSubject
     */
    public function functions(Closure|string $function): FunctionSubject
    {
        return new FunctionSubject($function, $this);
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

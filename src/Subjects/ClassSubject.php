<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Comcode;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
use PhpParser\Node;

class ClassSubject extends SubjectAbstract
{
    /**
     * @param  object|string  $class
     * @param  FileSubject  $fileSubject
     */
    public function __construct(
        public object|string $class,
        protected FileSubject $fileSubject,
    ) {
        /**
         * Create nodes
         */
        parent::__construct(
            (new ParserFactory())
                ->create(ParserFactory::PREFER_PHP7)
                ->parse(
                    file_get_contents($this->fileSubject->file)
                )
        );
    }

    /**
     * Save nodes to file
     * @return bool|int
     */
    public function save(): bool|int
    {
        return file_put_contents(
            $this->fileSubject->file,
            (string) $this
        );
    }

    /**
     * Discover individual environment
     * @return void
     */
    protected function discoverStmtEnvironment(): void
    {
        $existsNamespaceName = false;
        $namespaceName = Comcode::classNamespaceName($this->class);
        foreach ($this->nodes as $node) {
            if ($node instanceof Namespace_) {
                $node->name->parts = explode(
                    "\\",
                    $namespaceName
                );
                $existsNamespaceName = true;
                break;
            }
        }
        if (!$existsNamespaceName) {
            $this->nodes = [
                new Namespace_(
                    new Node\Name($namespaceName)
                ),
                ...$this->nodes,
            ];
        }
    }
}

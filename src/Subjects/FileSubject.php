<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\CSFixer;
use Bfg\Comcode\FileParser;
use ErrorException;

class FileSubject
{
    /**
     * @var CSFixer
     */
    public CSFixer $fixer;

    /**
     * @param  string  $file
     * @throws ErrorException
     */
    public function __construct(
        public string $file,
    ) {
        $this->fixer = CSFixer::new($this);
    }

    /**
     * CHILDHOOD FUNCTION
     * @param  object|string|null  $class
     * @return ClassSubject
     * @throws ErrorException
     */
    public function class(
        object|string $class = null
    ): ClassSubject {
        return (new ClassSubject(
            $this, $class
            ?: (new FileParser)
                ->getClassFullNameFromFile($this->file)
        ));
    }

    /**
     * Delete fool file
     * @return bool
     */
    public function delete(): bool
    {
        return unlink($this->file);
    }

    /**
     * Update content in file
     * @param  string|null  $content
     * @return static
     */
    public function update(string $content = null): static
    {
        file_put_contents(
            $this->file,
            !is_null($content) ? $content."\n" : ""
        );

        return $this;
    }

    /**
     * @return string|null
     */
    public function fix(): ?string
    {
        return $this->fixer->run();
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return file_get_contents($this->file) ?? '';
    }
}

<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Comcode;
use Bfg\Comcode\FileParser;
use Bfg\Comcode\FixStandard;
use ErrorException;
use SplFileInfo;

class FileSubject
{
    /**
     * @var FixStandard
     */
    public FixStandard $fixer;

    /**
     * @var SplFileInfo
     */
    public SplFileInfo $info;

    /**
     * @param  string  $file
     * @throws ErrorException
     */
    public function __construct(
        public string $file,
    ) {
        $this->fixer = FixStandard::new($this);
        $this->info = new SplFileInfo($this->file);
    }

    /**
     * CHILDHOOD FUNCTION
     * @param  string|null  $class
     * @return ClassSubject
     */
    public function class(
        string $class = null
    ): ClassSubject {
        Comcode::emit('create-subject', $class);
        return (new ClassSubject(
            $this, $class
            ?: (new FileParser)
                ->getClassFullNameFromFile($this->file)
        ));
    }

    /**
     * CHILDHOOD FUNCTION
     * @param  string|null  $class
     * @return TraitSubject
     */
    public function trait(
        string $class = null
    ): TraitSubject {
        Comcode::emit('create-subject', $class);
        return (new TraitSubject(
            $this, $class
            ?: (new FileParser)
                ->getClassFullNameFromFile($this->file)
        ));
    }

    /**
     * CHILDHOOD FUNCTION
     * @param  string|null  $class
     * @return EnumSubject
     */
    public function enum(
        string $class = null,
    ): EnumSubject {
        Comcode::emit('create-subject', $class);
        return (new EnumSubject(
            $this,
            $class
            ?: (new FileParser)
                ->getClassFullNameFromFile($this->file)
        ));
    }

    /**
     * CHILDHOOD FUNCTION
     * @param  string|null  $class
     * @return InterfaceSubject
     */
    public function interface(
        string $class = null
    ): InterfaceSubject {
        Comcode::emit('create-subject', $class);
        return (new InterfaceSubject(
            $this, $class
            ?: (new FileParser)
                ->getClassFullNameFromFile($this->file)
        ));
    }

    /**
     * CHILDHOOD FUNCTION
     * @param  string|null  $namespace
     * @return AnonymousClassSubject
     */
    public function anonymousClass(
        ?string $namespace = null
    ): AnonymousClassSubject {
        Comcode::emit('create-subject', $namespace);
        return (new AnonymousClassSubject(
            $this, $namespace ?: ''
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
     * @return $this
     */
    public function fix(): static
    {
        $this->fixer->fix();

        return $this;
    }

    /**
     * @return $this
     */
    public function standard(): static
    {
        $this->fixer->standard();

        return $this;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return file_get_contents($this->file) ?? '';
    }

    /**
     * @return SplFileInfo
     */
    public function info(): SplFileInfo
    {
        return $this->info;
    }
}

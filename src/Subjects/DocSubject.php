<?php

namespace Bfg\Comcode\Subjects;

use Bfg\Comcode\Comcode;
use Exception;

class DocSubject
{
    /**
     * Doc name.
     *
     * @var null|string
     */
    protected ?string $doc_name = null;

    /**
     * Doc description.
     *
     * @var null|string
     */
    protected ?string $doc_description = null;

    /**
     * Doc entity collection.
     *
     * @var array
     */
    protected array $docs = [];

    /**
     * @param  ClassSubject|null  $classSubject
     */
    public function __construct(
        public ?ClassSubject $classSubject = null,
    ) {
    }

    /**
     * Add doc name.
     *
     * @param  string  $name
     * @return $this
     */
    public function name(
        string $name
    ): static {
        $this->doc_name = $name;
        return $this;
    }

    /**
     * Add doc description.
     *
     * @param  string  $description
     * @return $this
     */
    public function description(
        string $description
    ): static {
        $this->doc_description = $description;
        return $this;
    }

    /**
     * The "api" tag is used to declare Structural Elements as being suitable for
     * consumption by third parties.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/api.html
     * @return $this
     */
    public function tagApi(): static
    {
        $this->docs['api'][] = '';

        return $this;
    }

    /**
     * Use the "abstract" tag to declare a class as abstract, as well as for declaring what methods must be redefined in a child class.
     *
     * @link https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.abstract.pkg.html
     * @return $this
     */
    public function tagAbstract(): static
    {
        $this->docs['abstract'][] = '';

        return $this;
    }

    /**
     * If "access" is private, the element will not be documented unless specified by command-line switch --parseprivate.
     *
     * @param  array|string  $modifiers
     * @return $this
     */
    public function tagAccess(array|string $modifiers): static
    {
        $this->docs['access'][] = is_array($modifiers) ? implode('|', $modifiers) : $modifiers;

        return $this;
    }

    /**
     * The "author" tag is used to document the author of Structural Elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/author.html
     * @param  string  $name
     * @param  null  $email
     * @return $this
     */
    public function tagAuthor(string $name, $email = null): static
    {
        $this->docs['author'][] = $name.($email ? ' <'.$email.'>' : '');

        return $this;
    }

    /**
     * The "copyright" tag is used to document the copyright information for Structural elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/copyright.html
     * @param  int|string  $year
     * @param  string|null  $copyright
     * @return $this
     */
    public function tagCopyright(int|string $year, string $copyright = null): static
    {
        $this->docs['copyright'][] = $year.($copyright ? ' '.$copyright : '');

        return $this;
    }

    /**
     * The "deprecated" tag is used to indicate which Structural elements are deprecated and
     * are to be removed in a future version.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/deprecated.html
     * @param  string  $comment
     * @return $this
     */
    public function tagDeprecated(string $comment): static
    {
        $this->docs['deprecated'][] = $comment;

        return $this;
    }

    /**
     * The "example" tag shows the code of a specified example file, or optionally, just a portion of it.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/example.html
     * @param  string  $comment
     * @return $this
     */
    public function tagExample(string $comment): static
    {
        $this->docs['example'][] = $comment;

        return $this;
    }

    /**
     * The "filesource" tag is used to tell phpDocumentor to include the source of the current
     * file in the parsing results.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/filesource.html
     * @return $this
     */
    public function tagFilesource(): static
    {
        $this->docs['filesource'][] = '';

        return $this;
    }

    /**
     * The "internal" tag is used to denote that associated Structural Elements are elements internal to
     * this application or library. It may also be used inside a long description to insert a piece of
     * text that is only applicable for the developers of this software.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/internal.html
     * @param  string  $description
     * @return $this
     */
    public function tagInternal(string $description): static
    {
        $this->docs['internal'][] = $description;

        return $this;
    }

    /**
     * The "license" tag is used to indicate which license is applicable for the associated Structural Elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/license.html
     * @param  string  $url
     * @param  string|null  $name
     * @return $this
     */
    public function tagLicense(string $url, string $name = null): static
    {
        $this->docs['license'][] = $url.($name ? ' '.$name : '');

        return $this;
    }

    /**
     * The "method" allows a class to know which ‘magic’ methods are callable.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/method.html
     * @param  string  $type
     * @param  string  $method
     * @param  string|null  $description
     * @return $this
     */
    public function tagMethod(string $type, string $method, string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['method'][] = $type.' '.$method.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "package" tag is used to categorize Structural Elements into logical subdivisions.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/package.html
     * @param  string  $namespace
     * @return $this
     */
    public function tagPackage(string $namespace): static
    {
        $this->docs['package'][] = $namespace;

        return $this;
    }

    /**
     * The "param" tag is used to document a single argument of a function or method.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/param.html
     * @param  string  $type
     * @param  string  $param
     * @param  string|null  $description
     * @return $this
     */
    public function tagParam(string $type, string $param = '', string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['param'][] = $type.(!empty($param) ? ' $'.$param : '').($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "property" tag allows a class to know which ‘magic’ properties are present.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/property.html
     * @param  string  $type
     * @param  string  $param
     * @param  string|null  $description
     * @return $this
     */
    public function tagProperty(string $type, string $param = '', string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['property'][] = $type.(!empty($param) ? ' $'.$param : '').($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "property-read" tag allows a class to know which ‘magic’ properties are present that are read-only.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/property-read.html
     * @param  string  $type
     * @param  string  $param
     * @param  string|null  $description
     * @return $this
     */
    public function tagPropertyRead(string $type, string $param = '', string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['property-read'][] = $type.(!empty($param) ? ' $'.$param : '').($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "property-write" tag allows a class to know which ‘magic’ properties are present that are write-only.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/property-write.html
     * @param  string  $type
     * @param  string  $param
     * @param  string|null  $description
     * @return $this
     */
    public function tagPropertyWrite(string $type, string $param = '', string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['property-write'][] = $type.(!empty($param) ? ' $'.$param : '').($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "return" tag is used to document the return value of functions or methods.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/return.html
     * @param  string  $type
     * @param  string|null  $description
     * @return $this
     */
    public function tagReturn(string $type, string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['return'][] = $type.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "see" tag indicates a reference from the associated Structural Elements to a website or other Structural Elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/see.html
     * @param  string  $subject
     * @param  string|null  $description
     * @return $this
     */
    public function tagSee(string $subject, string $description = null): static
    {
        $this->docs['see'][] = $subject.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "since" tag indicates at which version did the associated Structural Elements became available.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/since.html
     * @param  string  $version
     * @param  string|null  $description
     * @return $this
     */
    public function tagSince(string $version, string $description = null): static
    {
        $this->docs['since'][] = $version.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "source" tag shows the source code of Structural Elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/source.html
     * @param  string|int  $start_line
     * @param  string|int  $numbers_of_lines
     * @param  string|null  $description
     * @return $this
     */
    public function tagSource(string|int $start_line, string|int $numbers_of_lines, string $description = null): static
    {
        $this->docs['source'][] = $start_line.' '.$numbers_of_lines.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "todo" tag is used to indicate whether any development activities should still be executed on associated Structural Elements.
     *
     * @param  string  $description
     * @return $this
     */
    public function tagTodo(string $description): static
    {
        $this->docs['todo'][] = $description;

        return $this;
    }

    /**
     * The "uses" tag indicates a reference to (and from) a single associated Structural Elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/uses.html
     * @param  string  $fqsen
     * @param  string|null  $description
     * @return $this
     */
    public function tagUses(string $fqsen, string $description = null): static
    {
        $this->docs['uses'][] = $fqsen.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "uses" tag indicates a reference to (and from) a single associated Structural Elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/uses.html
     * @param  string  $fqsen
     * @param  string|null  $description
     * @return $this
     */
    public function tagUsesBy(string $fqsen, string $description = null): static
    {
        $this->docs['uses-by'][] = $fqsen.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * You may use the "var" tag to document the “Type” of properties, sometimes called class variables.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/var.html
     * @param $type
     * @param  null|string  $param
     * @param  null|string  $description
     * @return $this
     */
    public function tagVar($type, string $param = null, string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['var'][] = $type.($param ? ' $'.$param : '').($description ? ' '.$description : '');

        return $this;
    }

    /**
     * The "version" tag indicates the current version of Structural Elements.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/version.html
     * @param  string  $vector
     * @param  string|null  $description
     * @return $this
     */
    public function tagVersion(string $vector, string $description = null): static
    {
        $this->docs['version'][] = $vector.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * @return $this
     */
    public function throwException(): static
    {
        $this->tagThrows("\Exception");

        return $this;
    }

    /**
     * The "throws" tag is used to indicate whether Structural Elements could throw a specific type of exception.
     *
     * @link http://docs.phpdoc.org/references/phpdoc/tags/throws.html
     * @param  string  $type
     * @param  string|null  $description
     * @return $this
     */
    public function tagThrows(string $type, string $description = null): static
    {
        $type = Comcode::useIfClass($type, $this->classSubject);

        $this->docs['throws'][] = $type.($description ? ' '.$description : '');

        return $this;
    }

    /**
     * Magic call.
     *
     * @param $name
     * @param $arguments
     * @return $this
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^tag([A-Z][A-Za-z_]+)$/', $name, $m)) {
            return $this->tagCustom(strtolower($m[1]), implode(' ', $arguments));
        }

        return $this;
    }

    /**
     * Add custom tag.
     *
     * @param $tag_name
     * @param  null  $tag_data
     * @return $this
     */
    public function tagCustom($tag_name, $tag_data = null): static
    {
        $this->docs[$tag_name][] = $tag_data ?: '';

        return $this;
    }

    /**
     * Build entity.
     *
     * @return string
     */
    public function render(): string
    {
        $begin = ' * ';
        $data = '/**'.PHP_EOL;
        $has = false;

        if ($this->doc_name) {
            $data .= $begin.$this->doc_name.PHP_EOL;
            $has = true;
        }

        if ($this->doc_description) {
            $data .= $begin.$this->doc_description.PHP_EOL;
            $has = true;
        }

        foreach ($this->docs as $tag => $tagList) {
            foreach ($tagList as $item) {
                $data .= $begin.'@'.trim($tag, '@').' '.$item.PHP_EOL;
                $has = true;
            }
        }
        $data .= ' */';

        return $has ? $data : '';
    }
}

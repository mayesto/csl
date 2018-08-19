<?php

namespace Mayesto\CSL\Rule;

use Mayesto\CSL\OutputMessage\Error;
use Mayesto\CSL\OutputMessage\Warning;
use Mayesto\CSL\RuleInterface;
use PhpParser;
use Mayesto\CSL\File;
use Mayesto\CSL\AstTravers\FindClasses;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class ClassPhpDocPropertyRequire implements RuleInterface
{
    /**
     * @var string
     */
    private $regex;

    /**
     * @var \Closure
     */
    private $messageContentGetter;

    /**
     * ClassPhpDocPropertyRequire constructor.
     *
     * @param string $regex
     * @param \Closure $messageContentGetter
     */
    public function __construct(string $regex, \Closure $messageContentGetter)
    {
        $this->regex = $regex;
        $this->messageContentGetter = $messageContentGetter;
    }

    /**
     * @param \Mayesto\CSL\File $file
     *
     * @return \Generator|\Mayesto\CSL\OutputMessage
     */
    public function check(File $file): \Generator
    {
        $parser = (new PhpParser\ParserFactory)->create(PhpParser\ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($file->getContent());
            $findClasses = new FindClasses();
            $findClasses->traverse($ast);

            foreach ($findClasses->getClasses() as $class) {
                $comments = $class->getAttribute('comments');
                if (\is_array($comments) && \count($comments) === 1) {
                    $has = false;
                    foreach (\explode(PHP_EOL, $comments[0]) as $line) {
                        if (\preg_match($this->regex, $line)) {
                            $has = true;
                            break;
                        }
                    }
                    if (false === $has) {
                        yield new Warning(
                            $this,
                            $file,
                            $class->name->getAttribute('startLine'),
                            $this->messageContentGetter->call($this, $class)
                        );
                    }
                }
            }
        } catch (PhpParser\Error $error) {
            yield new Error($this, $file, $error->getLine(), $error->getMessage());
        }
    }
}

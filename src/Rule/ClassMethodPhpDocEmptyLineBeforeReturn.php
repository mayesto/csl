<?php


namespace Mayesto\CSL\Rule;

use Mayesto\CSL\AstTravers\FindClasses;
use Mayesto\CSL\AstTravers\FindMethods;
use Mayesto\CSL\File;
use Mayesto\CSL\OutputMessage\Error;
use Mayesto\CSL\OutputMessage\Warning;
use Mayesto\CSL\RuleInterface;
use PhpParser;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class ClassMethodPhpDocEmptyLineBeforeReturn implements RuleInterface
{
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
                $findMethods = new FindMethods();
                $findMethods->traverse([$class]);

                foreach ($findMethods->getClassMethods() as $method) {
                    $comments = $method->getAttribute('comments');
                    if (\is_array($comments)) {
                        $generator = $this->parseComment($file, $comments[0], $method);
                        foreach ($generator as $item) {
                            yield $item;
                        }
                    }
                }
            }
        } catch (PhpParser\Error $error) {
            yield new Error($this, $file, $error->getLine(), $error->getMessage());
        }
    }

    /**
     * @param \Mayesto\CSL\File $file
     * @param \PhpParser\Comment $comment
     * @param \PhpParser\Node\Stmt\ClassMethod $method
     *
     * @return \Generator
     */
    protected function parseComment(
        File $file,
        PhpParser\Comment $comment,
        PhpParser\Node\Stmt\ClassMethod $method
    ): \Generator {
        $text = $comment->getText();
        if (\preg_match('/@return|@throws/', $text)) {
            $lines = \explode("\n", $text);
            \array_pop($lines);
            \array_shift($lines);
            $hasTag = false;
            $hasOther = false;
            $hasEmptyLine = false;
            foreach ($lines as $index => $line) {
                if (\preg_match('/@([a-z]+)/', $line, $matches)) {
                    if ($matches[1] === 'return' || $matches[1] === 'throws') {
                        $hasTag = true;
                    } elseif ($hasTag && $hasEmptyLine) {
                        $hasOther = true;
                        yield new Warning(
                            $this,
                            $file,
                            $comment->getLine() + $index + 1,
                            "Method {$method->name} has invalid tag \"{$matches[1]}\""
                            . " after @return or @throws"
                        );
                    } else {
                        $hasOther = true;
                    }
                }
                if (\preg_match("/^[\s]*\*[\s]*$/", $line)) {
                    if ($hasEmptyLine && $hasTag) {
                        yield new Warning(
                            $this,
                            $file,
                            $comment->getLine() + $index + 1,
                            "Empty line between @return and @throws is not allowed"
                        );
                    }
                    $hasEmptyLine = true;
                }
            }
            if ($hasTag && $hasOther && !$hasEmptyLine) {
                yield new Warning(
                    $this,
                    $file,
                    $method->name->getAttribute('startLine'),
                    "Method {$method->name} has not empty line before @return or @throws"
                );
            }
        }
    }
}

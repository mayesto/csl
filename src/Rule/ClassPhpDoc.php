<?php

namespace Mayesto\CSL\Rule;

use Mayesto\CSL\AstTravers\FindClasses;
use Mayesto\CSL\File;
use Mayesto\CSL\OutputMessage\Error;
use Mayesto\CSL\OutputMessage\Warning;
use Mayesto\CSL\RuleInterface;
use PhpParser;

/**
 * @author Mayesto <m@mayesto.pl>
 * @todo fullclassname in message
 */
class ClassPhpDoc implements RuleInterface
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
                $comments = $class->getAttribute('comments');
                if (\is_null($comments)) {
                    yield new Warning(
                        $this,
                        $file,
                        $class->name->getAttribute('startLine'),
                        "Class {$class->name} has not php doc"
                    );
                } elseif (\count($comments) > 1) {
                    yield new Warning(
                        $this,
                        $file,
                        $class->name->getAttribute('startLine'),
                        "Class {$class->name} has greater than one php doc"
                    );
                }
            }
        } catch (PhpParser\Error $error) {
            yield new Error($this, $file, $error->getLine(), $error->getMessage());
        }
    }
}

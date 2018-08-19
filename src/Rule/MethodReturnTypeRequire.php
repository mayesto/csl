<?php

namespace Mayesto\CSL\Rule;

use Mayesto\CSL\File;
use Mayesto\CSL\RuleInterface;
use Mayesto\CSL\AstTravers\FindClasses;
use Mayesto\CSL\AstTravers\FindMethods;
use Mayesto\CSL\OutputMessage\Error;
use Mayesto\CSL\OutputMessage\Warning;
use PhpParser;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class MethodReturnTypeRequire implements RuleInterface
{
    private $excludedMethods = ['__construct'];

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
                    if (\in_array((string)$method->name, $this->excludedMethods)) {
                        continue;
                    }
                    if (\is_null($method->getReturnType())) {
                        yield new Warning(
                            $this,
                            $file,
                            $method->name->getAttribute('startLine'),
                            "Method {$method->name} has not return type cast"
                        );
                    }
                }
            }
        } catch (PhpParser\Error $error) {
            yield new Error($this, $file, $error->getLine(), $error->getMessage());
        }
    }
}

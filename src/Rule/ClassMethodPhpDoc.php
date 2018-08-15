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
class ClassMethodPhpDoc implements RuleInterface
{
    private $excludedMethods = ['__construct'];

    /**
     * @param \Mayesto\CSL\File $file
     *
     * @todo check doc in parents
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
                    $comments = $method->getAttribute('comments');
                    if (\is_null($comments)) {
//                        @todo problem with find full class name of extends or implements and autoloader
//                        if (!$this->shouldHaveDocBlock($class, $method)) {
//                            continue;
//                        }
                        yield new Warning(
                            $this,
                            $file,
                            $method->name->getAttribute('startLine'),
                            "Method {$method->name} has not php doc"
                        );
                    } elseif (\count($comments) > 1) {
                        yield new Warning(
                            $this,
                            $file,
                            $method->name->getAttribute('startLine'),
                            "Method {$method->name} has greater than one php doc"
                        );
                    }
                }
            }
        } catch (PhpParser\Error $error) {
            yield new Error($this, $file, $error->getLine(), $error->getMessage());
        }
    }

//    private function shouldHaveDocBlock(PhpParser\Node\Stmt\Class_ $class, PhpParser\Node $method): bool
//    {
//        $classesWhichHasMethod = $this->getClassesWhichHaveMethod(
//            $this->getClassParents($class, $method),
//            $method
//        );
//
//        foreach ($classesWhichHasMethod as $className) {
//            if ($this->checkDocBlockInMethod($className, $method)) {
//
//                return false;
//            }
//        }
//
//        return true;
//    }

//    private function checkDocBlockInMethod(string $className, PhpParser\Node $method)
//    {
//        var_dump($className);
//        if (class_exists($className)) {
//
//        }
//
//        return false;
//    }

//    /**
//     * @param string $classNames
//     * @param \PhpParser\Node $method
//     *
//     * @return array
//     */
//    private function getClassesWhichHaveMethod(array $classNames, PhpParser\Node $method): array
//    {
//        return array_filter(
//            $classNames,
//            function ($className) use ($method) {
//                return method_exists($className, (string)$method->name);
//            }
//        );
//    }

//    private function getClassParents(PhpParser\Node\Stmt\Class_ $class, PhpParser\Node $method): array
//    {
//        $namespace = (string)$class->getAttribute('parent')->name;
//        $fullClassName = $namespace . '\\' . $class->name;
//        if ($class->extends) {
//            var_dump(
//                $class->extends->isFullyQualified(),
//                (string)$class->extends,
//                $class->extends->getAttribute('parent')->getAttribute('parent')
//            );
//        }
//
//        $classesToCheck = array_map(
//            function ($class) {
//                return (string)$class;
//            },
//            array_filter(
//                array_merge([$class->extends], $class->implements),
//                function ($class) {
//                    return !is_null($class);
//                }
//            )
//        );
//
//        return $classesToCheck;
//    }
}

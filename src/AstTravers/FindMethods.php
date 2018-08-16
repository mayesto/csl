<?php

namespace Mayesto\CSL\AstTravers;

use PhpParser;

/**
 * Class FindMethods
 *
 * @author Mayesto <m@mayesto.pl>
 * @package Mayesto\CSL\Rule\ClassPhpDoc
 */
class FindMethods extends PhpParser\NodeVisitorAbstract
{
    /**
     * @var \PhpParser\Node\Stmt\ClassMethod[]
     */
    private $classMethods = [];

    public function leaveNode(PhpParser\Node $node): void
    {
        if ($node instanceof PhpParser\Node\Stmt\ClassMethod) {
            $this->classMethods[] = $node;
        }
    }

    public function traverse($ast): array
    {
        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor($this);

        return $traverser->traverse($ast);
    }

    /**
     * @return \PhpParser\Node\Stmt\ClassMethod[]
     */
    public function getClassMethods(): array
    {
        return $this->classMethods;
    }
}

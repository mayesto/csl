<?php

namespace Mayesto\CSL\AstTravers;

use PhpParser;

/**
 * Class FindClasses
 *
 * @author Mayesto <m@mayesto.pl>
 * @package Mayesto\CSL\Rule\ClassPhpDoc
 */
class FindClasses extends PhpParser\NodeVisitorAbstract
{
    /**
     * @var \PhpParser\Node\Stmt\Class_[]
     */
    private $classes = [];

    /**
     * @var PhpParser\Node[]
     */
    private $stack;

    public function beforeTraverse(array $nodes): void
    {
        $this->stack = [];
    }

    public function enterNode(PhpParser\Node $node): void
    {
        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[\count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }

    public function leaveNode(PhpParser\Node $node): void
    {
        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            $this->classes[] = $node;
        }
        \array_pop($this->stack);
    }

    /**
     * @param array $ast
     *
     * @return array
     */
    public function traverse(array $ast): array
    {
        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor($this);

        return $traverser->traverse($ast);
    }

    /**
     * @return \PhpParser\Node\Stmt\Class_[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }
}

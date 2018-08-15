<?php

namespace Mayesto\CSL\AstTravers;

use PhpParser;

/**
 * Class FindCalls
 *
 * @author mayesto <m@mayesto.pl>
 * @package Mayesto\CSL\AstTravers
 */
class FindCalls extends PhpParser\NodeVisitorAbstract
{
    /**
     * @var \PhpParser\Node\Expr\FuncCall[]
     */
    private $calls = [];

    public function leaveNode(PhpParser\Node $node): void
    {
        if ($node instanceof PhpParser\Node\Expr\FuncCall) {
            $this->calls[] = $node;
        }
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
     * @return \PhpParser\Node\Expr\FuncCall[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }
}

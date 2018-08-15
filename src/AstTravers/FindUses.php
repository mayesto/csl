<?php

namespace Mayesto\CSL\AstTravers;

use PhpParser;

/**
 * Class FindUses
 *
 * @author mayesto <m@mayesto.pl>
 * @package Mayesto\CSL\Rule\InternalFunctionNamespace
 */
class FindUses extends PhpParser\NodeVisitorAbstract
{
    /**
     * @var FindUses\UseItem[]
     */
    private $uses = [];

    public function leaveNode(PhpParser\Node $node): void
    {
        if ($node instanceof PhpParser\Node\Stmt\Use_) {
            foreach ($node->uses as $use) {
                if (\is_null($use->alias)) {
                    $this->uses[] = new FindUses\UseItem($node->type, null, \implode("\\", $use->name->parts));
                } else {
                    $this->uses[] = new FindUses\UseItem(
                        $node->type,
                        $use->alias->name,
                        \implode("\\", $use->name->parts)
                    );
                }
            }
        }
    }

    public function traverse($ast): array
    {
        $traverser = new PhpParser\NodeTraverser;
        $traverser->addVisitor($this);

        return $traverser->traverse($ast);
    }

    /**
     * @return FindUses\UseItem[]
     */
    public function getUses(): array
    {
        return $this->uses;
    }
}

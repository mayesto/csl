<?php


namespace Mayesto\CSL\Rule;

use Mayesto\CSL\AstTravers\FindCalls;
use Mayesto\CSL\AstTravers\FindUses;
use Mayesto\CSL\AstTravers\FindUses\UseItem;
use Mayesto\CSL\File;
use Mayesto\CSL\OutputMessage\Error;
use Mayesto\CSL\OutputMessage\Warning;
use Mayesto\CSL\RuleInterface;
use PhpParser;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class InternalFunctionNamespace implements RuleInterface
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

            $findUses = new FindUses();
            $findUses->traverse($ast);
            $findCalls = new FindCalls();
            $findCalls->traverse($ast);
            $functions = \get_defined_functions()['internal'];
            foreach ($findCalls->getCalls() as $call) {
                if (!$call->name instanceof PhpParser\Node\Name\FullyQualified) {
                    $functionName = $call->name->parts[0];

                    if (\in_array($functionName, $functions)) {
                        $result = \array_filter(
                            $findUses->getUses(),
                            function (UseItem $useItem) use ($functionName) {
                                return $useItem->getType() === UseItem::TYPE_FUNCTION
                                    && $useItem->getResource() === $functionName;
                            }
                        );
                        if (0 === \count($result)) {
                            yield new Warning(
                                $this,
                                $file,
                                $call->name->getAttribute('startLine'),
                                "call {$functionName} is ambiguous"
                            );
                        }
                    }
                }
            }
        } catch (PhpParser\Error $error) {
            yield new Error($this, $file, $error->getLine(), $error->getMessage());
        }
    }
}

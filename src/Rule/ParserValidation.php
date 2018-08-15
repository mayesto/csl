<?php


namespace Mayesto\CSL\Rule;

use Mayesto\CSL\File;
use Mayesto\CSL\OutputMessage\Error;
use Mayesto\CSL\RuleInterface;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class ParserValidation implements RuleInterface
{

    /**
     * @param \Mayesto\CSL\File $file
     *
     * @return \Generator|\Mayesto\CSL\OutputMessage
     */
    public function check(File $file): \Generator
    {
        try {
            \token_get_all($file->getContent(), TOKEN_PARSE);
        } catch (\Error $error) {
            yield new Error($this, $file, $error->getLine(), $error->getMessage());
        }
    }
}

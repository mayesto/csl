<?php

namespace Mayesto\CSL\Rule;

use Mayesto\CSL\File;
use Mayesto\CSL\OutputMessage\Warning;
use Mayesto\CSL\RuleInterface;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class EmptyLineOnEndOfFile implements RuleInterface
{
    /**
     * @param \Mayesto\CSL\File $file
     *
     * @return \Generator|\Mayesto\CSL\OutputMessage
     */
    public function check(File $file): \Generator
    {
        $generator = $file->getLine();

        while ($generator->valid()) {
            /**
             * @var \Mayesto\CSL\File\Line $lastValue
             */
            $lastValue = $generator->current();
            $generator->next();
        }
        if (\strpos($lastValue->getContent(), PHP_EOL) === false) {
            yield new Warning(
                $this,
                $file,
                $lastValue->getLineNumber(),
                'File must have empty line on end of file'
            );
        }
    }
}

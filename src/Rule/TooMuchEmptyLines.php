<?php

namespace Mayesto\CSL\Rule;

use Mayesto\CSL\File;
use Mayesto\CSL\OutputMessage\Warning;
use Mayesto\CSL\RuleInterface;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class TooMuchEmptyLines implements RuleInterface
{
    /**
     * @var int number of empty lines generating an error
     */
    private $border;

    /**
     * TooMuchEmptyLines constructor.
     *
     * @param int $border
     */
    public function __construct(int $border = 2)
    {
        $this->border = $border;
    }

    /**
     * @param \Mayesto\CSL\File $file
     *
     * @return \Generator|\Mayesto\CSL\OutputMessage
     */
    public function check(File $file): \Generator
    {
        $blankLines = 0;
        foreach ($file->getLine() as $line) {
            /**
             * @var \Mayesto\CSL\File\Line $line
             */
            if (\trim($line->getContent()) === "") {
                $blankLines++;
            } else {
                if ($blankLines >= $this->border) {
                    yield new Warning(
                        $this,
                        $file,
                        $line->getLineNumber(),
                        'Too much empty lines'
                    );
                }
                $blankLines = 0;
            }
        }
    }
}

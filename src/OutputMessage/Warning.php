<?php

namespace Mayesto\CSL\OutputMessage;

use Mayesto\CSL\OutputMessage;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class Warning extends AbstractOutputMessage implements OutputMessage
{
    /**
     * @return string
     */
    public static function getTypeString(): string
    {
        return "WARNING";
    }
}

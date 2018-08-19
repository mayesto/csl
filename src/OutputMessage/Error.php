<?php

namespace Mayesto\CSL\OutputMessage;

use Mayesto\CSL\OutputMessage;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class Error extends AbstractOutputMessage implements OutputMessage
{
    /**
     * @return string
     */
    public static function getTypeString(): string
    {
        return "ERROR";
    }
}

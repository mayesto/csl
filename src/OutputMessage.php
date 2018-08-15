<?php


namespace Mayesto\CSL;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
interface OutputMessage
{
    /**
     * @return \Mayesto\CSL\RuleInterface
     */
    public function getRule(): RuleInterface;

    /**
     * @return \Mayesto\CSL\File
     */
    public function getFile(): File;

    /**
     * @return int
     */
    public function getLine(): int;

    /**
     * @return string
     */
    public function getMessageContent(): string;
}

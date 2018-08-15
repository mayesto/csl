<?php


namespace Mayesto\CSL\OutputMessage;

use Mayesto\CSL\File;
use Mayesto\CSL\RuleInterface;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
abstract class AbstractOutputMessage implements \JsonSerializable
{
    /**
     * @var \Mayesto\CSL\RuleInterface
     */
    private $rule;
    /**
     * @var \Mayesto\CSL\File
     */
    private $file;
    /**
     * @var int
     */
    private $line;
    /**
     * @var string
     */
    private $messageContent;

    /**
     * AbstractOutputMessage constructor.
     *
     * @param \Mayesto\CSL\RuleInterface $rule
     * @param \Mayesto\CSL\File $file
     * @param int $line
     * @param string $messageContent
     */
    public function __construct(RuleInterface $rule, File $file, int $line, string $messageContent)
    {
        $this->rule = $rule;
        $this->file = $file;
        $this->line = $line;
        $this->messageContent = $messageContent;
    }

    /**
     * @return \Mayesto\CSL\RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    /**
     * @return \Mayesto\CSL\File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getMessageContent(): string
    {
        return $this->messageContent;
    }

    public function jsonSerialize(): array
    {
        return [
            'rule' => \get_class($this->rule),
            'file' => $this->file->getPath(),
            'line' => $this->line,
            'content' => $this->messageContent
        ];
    }
}

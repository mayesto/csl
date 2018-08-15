<?php


namespace Mayesto\CSL\File;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class Line
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $content;

    /**
     * @var integer
     */
    private $lineNumber;

    /**
     * Line constructor.
     *
     * @param string $file
     * @param string $content
     * @param int $lineNumber
     */
    public function __construct(string $file, string $content, int $lineNumber)
    {
        $this->file = $file;
        $this->content = $content;
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}

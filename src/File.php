<?php

namespace Mayesto\CSL;

use Mayesto\CSL\Exception\FileException;
use Mayesto\CSL\File\Line;

/**
 * @author Mayesto <m@mayesto.pl>
 */
class File
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var integer
     */
    private $lines;

    /**
     * File constructor.
     *
     * @param string $path
     *
     * @throws \Mayesto\CSL\Exception\FileException
     */
    public function __construct(string $path)
    {
        if (!\file_exists($path)) {
            throw new FileException("File \"{$path}\" not exists");
        }
        if (!\is_file($path)) {
            throw new FileException("\"{$path}\" is not a file");
        }
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getLines(): int
    {
        return $this->lines;
    }

    /**
     * @return \Generator|\Mayesto\CSL\File\Line
     */
    public function getLine(): \Generator
    {
        $f = \fopen($this->path, 'r');

        $i = 0;
        while ($line = \fgets($f)) {
            $i++;
            yield new Line($this->path, $line, $i);
        }
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return \file_get_contents($this->path);
    }
}

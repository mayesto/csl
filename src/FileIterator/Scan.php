<?php


namespace Mayesto\CSL\FileIterator;

use Mayesto\CSL\Exception\FileException;
use Mayesto\CSL\File;
use Mayesto\CSL\FileIterator;

/**
 * Class Scan
 *
 * @package Mayesto\CSL
 * @author Mayesto <m@mayesto.pl>
 */
class Scan implements FileIterator
{
    /**
     * @return \Generator|\Mayesto\CSL\File[]|\Generator
     * @throws \Mayesto\CSL\Exception\FileException
     */
    public function getFile(string $path): \Generator
    {
        if (!\file_exists($path)) {
            throw new FileException("directory {$path} is not exits");
        }
        if (\is_dir($path)) {
            foreach ($this->scan($path) as $item) {
                yield $item;
            }
        } else {
            yield new File($path);
        }
    }

    /**
     * @param string $path
     *
     * @return \Generator
     * @throws \Mayesto\CSL\Exception\FileException
     */
    private function scan(string $path): \Generator
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                if ($fileinfo->isDir()) {
                    foreach ($this->scan($fileinfo->getRealPath()) as $file) {
                        yield $file;
                    }
                } else {
                    yield new File($fileinfo->getRealPath());
                }
            }
        }
    }
}

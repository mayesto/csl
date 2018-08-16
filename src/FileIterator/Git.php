<?php


namespace Mayesto\CSL\FileIterator;

use Mayesto\CSL\File;
use Mayesto\CSL\FileIterator;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class Git implements FileIterator
{
    /**
     * @param string $path
     *
     * @return \Generator
     * @throws \Exception
     */
    public function getFile(string $path): \Generator
    {
        $topLevel = $this->getTopLevel($path);
        foreach ($this->getFiles($path) as $file) {
            yield new File($topLevel . DIRECTORY_SEPARATOR . $file);
        }
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws \Exception
     */
    private function getTopLevel(string $path): string
    {
        $exitCode = 0;
        $output = null;
        \exec('git -C ' . $path . ' rev-parse --show-toplevel', $output, $exitCode);

        if ($exitCode > 0) {
            throw new \Exception('Unable to receive git top level path');
        }

        return $output[0];
    }

    /**
     * @param string $path
     *
     * @return \Generator|string[]
     * @throws \Exception
     */
    private function getFiles(string $path): \Generator
    {
        $exitCode = 0;
        $output = null;
        \exec('git -C ' . $path . ' status --porcelain | grep -v ??', $output, $exitCode);

        if ($exitCode > 0) {
            throw new \Exception('Unable to receive modified files from git');
        }

        foreach ($output as $item) {
            yield \substr($item, 3);
        }
    }
}

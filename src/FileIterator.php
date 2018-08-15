<?php

namespace Mayesto\CSL;

interface FileIterator
{
    public function getFile(string $path): \Generator;
}

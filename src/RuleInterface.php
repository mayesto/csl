<?php

namespace Mayesto\CSL;

interface RuleInterface
{
    /**
     * @param \Mayesto\CSL\File $file
     *
     * @return \Generator|\Mayesto\CSL\OutputMessage
     */
    public function check(File $file): \Generator;
}

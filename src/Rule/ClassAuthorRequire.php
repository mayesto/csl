<?php

namespace Mayesto\CSL\Rule;

use Mayesto\CSL\RuleInterface;

/**
 * @author Mayesto <m@mayesto.pl>
 */
class ClassAuthorRequire extends ClassPhpDocPropertyRequire implements RuleInterface
{
    public function __construct()
    {
        parent::__construct(
            '/@author/',
            function ($class) {
                return "Class \"{$class->name}\" has not information about author";
            }
        );
    }
}

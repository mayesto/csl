<?php

namespace Mayesto\CSL\Rule;

use Mayesto\CSL\RuleInterface;

/**
 * @author Mayesto <m@mayesto.pl>
 */
class ClassAuthorPattern extends ClassPhpDocPropertyRequire implements RuleInterface
{
    public function __construct($pattern)
    {
        parent::__construct(
            '/@author ' . $pattern . '/',
            function ($class) {
                return "Class \"{$class->name}\" has not valid information about author ";
            }
        );
    }
}

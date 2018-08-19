<?php

namespace Mayesto\CSL\AstTravers\FindUses;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class UseItem
{
    const TYPE_UNKNOWN = 0;
    /** Class or namespace import */
    const TYPE_NORMAL = 1;
    /** Function import */
    const TYPE_FUNCTION = 2;
    /** Constant import */
    const TYPE_CONSTANT = 3;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string|null
     */
    private $alias;

    /**
     * @var string
     */
    private $resource;

    /**
     * UseItem constructor.
     *
     * @param int $type
     * @param null|string $alias
     * @param string $resource
     */
    public function __construct(int $type, ?string $alias, string $resource)
    {
        $this->type = $type;
        $this->alias = $alias;
        $this->resource = $resource;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }
}

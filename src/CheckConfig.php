<?php

namespace Mayesto\CSL;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class CheckConfig
{
    /**
     * @var \Mayesto\CSL\RuleInterface[]
     */
    private $rules = [];

    /**
     * @var \Mayesto\CSL\FileIterator
     */
    private $fileIterator;

    /**
     * @var string
     */
    private $path;

    /**
     * @param \Mayesto\CSL\RuleInterface $rule
     *
     * @return \Mayesto\CSL\CheckConfig
     */
    public function addRule(RuleInterface $rule): CheckConfig
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @return \Mayesto\CSL\RuleInterface[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return \Mayesto\CSL\FileIterator
     */
    public function getFileIterator(): ?FileIterator
    {
        return $this->fileIterator;
    }

    /**
     * @param \Mayesto\CSL\FileIterator $fileIterator
     *
     * @return \Mayesto\CSL\CheckConfig
     */
    public function setFileIterator(FileIterator $fileIterator): CheckConfig
    {
        $this->fileIterator = $fileIterator;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}

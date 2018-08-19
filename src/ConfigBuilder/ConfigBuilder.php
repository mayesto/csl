<?php

namespace Mayesto\CSL\ConfigBuilder;

use Mayesto\CSL\CheckConfig;
use Mayesto\CSL\FileIterator;
use Mayesto\CSL\Exception\ConfigException;
use Mayesto\CSL\RuleInterface;

/**
 * @author Mayesto <m@mayesto.pl>
 *
 */
class ConfigBuilder
{
    /**
     * @param \stdClass $parsed
     *
     * @return \Mayesto\CSL\CheckConfig
     * @throws \ReflectionException
     * @throws \Mayesto\CSL\Exception\ConfigException
     */
    public function build(\stdClass $parsed): CheckConfig
    {
        $finalConfig = new CheckConfig();
        if (!empty($parsed->rules) && \is_array($parsed->rules)) {
            foreach ($parsed->rules as $className => $options) {
                $options = (object)$options;
                if (isset($options->file) && \file_exists($options->file)) {
                    include_once($options->file);
                }
                if (!\class_exists($className)) {
                    throw new \RuntimeException('rule class "' . $className . '" not exists');
                }
                $refClass = new \ReflectionClass($className);
                $hasConstructor = null !== $refClass->getConstructor();
                $constructorHasParameters = \count(
                    $hasConstructor ? $refClass->getConstructor()->getParameters() : []
                );
                if ($hasConstructor && $constructorHasParameters && \is_object($options)) {
                    $rule = $refClass->newInstance(...\array_values($options->arguments ?? []));
                } else {
                    $rule = $refClass->newInstance();
                }
                if (!$rule instanceof RuleInterface) {
                    throw new \RuntimeException('invalid rule class "' . $className . '"');
                }
                $finalConfig->addRule($rule);
            }
        }
        if (!empty($parsed->fileIterator)) {
            if (\class_exists($parsed->fileIterator)) {
                $finalConfig->setFileIterator(new $parsed->fileIterator);
            } else {
                throw new ConfigException('File iterator class "' . $parsed->fileIterator . '" is not exists');
            }
        } else {
            $finalConfig->setFileIterator(new FileIterator\Scan());
        }

        return $finalConfig;
    }
}

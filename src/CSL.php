<?php


namespace Mayesto\CSL;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class CSL
 *
 * @author Mayesto <m@mayesto.pl>
 * @package Mayesto\CSL
 */
class CSL implements LoggerAwareInterface
{
    /**
     * @var \Mayesto\CSL\CheckConfig
     */
    private $checkConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * CSL constructor.
     *
     * @param \Mayesto\CSL\CheckConfig $checkConfig
     */
    public function __construct(CheckConfig $checkConfig)
    {
        $this->checkConfig = $checkConfig;
        $this->logger = new NullLogger();
    }

    /**
     * @return \Mayesto\CSL\CheckResult
     * @throws \Mayesto\CSL\Exception\FileException
     */
    public function check(): CheckResult
    {
        $result = new CheckResult();

        $this->logger->debug('Start check for path: ' . $this->checkConfig->getPath());
        $time = \microtime(true);
        $files = 0;
        foreach ($this->checkConfig->getFileIterator()->getFile($this->checkConfig->getPath()) as $file) {
            $this->logger->debug('Check file: ' . $file->getPath());
            foreach ($this->checkConfig->getRules() as $rule) {
                $this->logger->debug("\tCheck rule: " . \get_class($rule));
                foreach ($rule->check($file) as $message) {
                    $result->addMessage($message);
                }
            }
            $files++;
        }
        $this->logger->info(\sprintf('Check finished after %.5f seconds', \microtime(true) - $time));
        $this->logger->info(\sprintf('Checked %d files', $files));

        return $result;
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}

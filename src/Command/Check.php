<?php


namespace Mayesto\CSL\Command;

use Mayesto\CSL\CheckConfig;
use Mayesto\CSL\CheckResult;
use Mayesto\CSL\ConfigBuilder;
use Mayesto\CSL\CSL;
use Mayesto\CSL\Exception\ConfigException;
use Mayesto\CSL\OutputMessage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Mayesto <m@mayesto.pl>
 */
class Check extends Command
{
    /**
     * @var \Mayesto\CSL\CheckConfig
     */
    private $config;

    public function configure(): void
    {
        $this->setName('check')
             ->addArgument('path', InputArgument::REQUIRED, 'Path do directory to scan')
             ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format [table|json]', 'table')
             ->addOption('yaml', null, InputOption::VALUE_REQUIRED, 'Path to yaml file with config');
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption('yaml')) {
            $parsed = Yaml::parseFile($input->getOption('yaml'));
        } else {
            throw new ConfigException('not found config option');
        }
        $configBuilder = new ConfigBuilder\ConfigBuilder();
        $this->config = $configBuilder->build((object)$parsed);
        $this->config->setPath($input->getArgument('path'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->config instanceof CheckConfig) {
            throw new \RuntimeException('not found configuration');
        }
        try {
            $csl = new CSL($this->config);
            if ($output->isVerbose()) {
                if ($output->isDebug()) {
                    $level = Logger::DEBUG;
                } elseif ($output->isVeryVerbose()) {
                    $level = Logger::INFO;
                } else {
                    $level = Logger::NOTICE;
                }
                $logger = new Logger(
                    'CSL',
                    [
                        new StreamHandler('php://stdout', $level)
                    ]
                );
                $csl->setLogger($logger);
            }
            $result = $csl->check();
            $resultCode = $this->calculateResultCode($result);
            if ($resultCode === 0) {
                $output->writeln("<info>Everything is ok!</info>");

                return $resultCode;
            }

            switch ($input->getOption('format')) {
                case 'table':
                    $this->renderTable($output, $result);
                    break;
                case 'json':
                    $this->renderJson($output, $result);
                    break;
                default:
                    throw new \RuntimeException('Invalid output format option');
            }

            return $resultCode;
        } catch (\Throwable $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');

            return 255;
        }
    }

    /**
     * @param \Mayesto\CSL\OutputMessage $message
     *
     * @return array
     */
    protected function getRowCells(OutputMessage $message): array
    {
        return [
            $this->decorate($message, $this->getTypeString($message)),
            $this->decorate($message, \get_class($message->getRule())),
            $this->decorate($message, $message->getFile()->getPath()),
            $this->decorate($message, $message->getLine()),
            $this->decorate($message, $message->getMessageContent())
        ];
    }

    private function decorate(OutputMessage $message, $value): string
    {
        switch (true) {
            case $message instanceof OutputMessage\Error:
                return "<error>{$value}</error>";
            case $message instanceof OutputMessage\Warning:
                return "<comment>{$value}</comment>";
            default:
                return $value;
        }
    }

    /**
     * @param \Mayesto\CSL\OutputMessage $message
     *
     * @throws
     * @return string
     */
    private function getTypeString(OutputMessage $message): string
    {
        switch (true) {
            case $message instanceof OutputMessage\Error:
                return "ERROR";
            case $message instanceof OutputMessage\Warning:
                return "WARNING";
            default:
                return "UNKNOWN";
        }
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Mayesto\CSL\CheckResult $result
     */
    private function renderTable(OutputInterface $output, CheckResult $result): void
    {
        $messagesTable = new Table($output);
        $messagesTable->setHeaders(['Type', 'Rule', 'File', 'Line', 'Message']);
        $stats = [];
        foreach ($result->getMessage() as $message) {
            $type = $this->getTypeString($message);
            if (!isset($stats[$type])) {
                $stats[$type] = 0;
            }
            $stats[$type]++;
            $messagesTable->addRow($this->getRowCells($message));
        }

        $messagesTable->render();
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Mayesto\CSL\CheckResult $result
     */
    private function renderJson(OutputInterface $output, CheckResult $result): void
    {
        $output->write(\json_encode($result));
    }

    /**
     * @param \Mayesto\CSL\CheckResult $result
     *
     * @return int
     */
    private function calculateResultCode(CheckResult $result): int
    {
        $hasError = false;
        $hasWarning = false;
        foreach ($result->getMessage() as $message) {
            if ($message instanceof OutputMessage\Error) {
                $hasError = true;
            } elseif ($message instanceof OutputMessage\Warning) {
                $hasWarning = true;
            }
        }

        if ($hasError) {
            return 1;
        } elseif ($hasWarning) {
            return 2;
        }

        return 0;
    }
}

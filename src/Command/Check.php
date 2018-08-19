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
             ->addOption('yaml', null, InputOption::VALUE_REQUIRED, 'Path to yaml file with config')
             ->addOption('short', 's', InputOption::VALUE_NONE, 'Short file path');
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
                    $this->renderTable($output, $this->getResultArray($input, $result));
                    break;
                case 'json':
                    $this->renderJson($output, $this->getResultArray($input, $result));
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

    private function decorate(array $message, $value): string
    {
        switch ($message['type']) {
            case OutputMessage\Error::getTypeString() :
                return "<error>{$value}</error>";
            case OutputMessage\Warning::getTypeString() :
                return "<comment>{$value}</comment>";
            default:
                return $value;
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Mayesto\CSL\CheckResult $result
     *
     * @return array
     */
    private function getResultArray(InputInterface $input, CheckResult $result): array
    {
        $isShort = $input->getOption('short');
        $path = $input->getArgument('path');

        return [
            'messages' => \array_map(
                function (OutputMessage $message) use ($isShort, $path) {
                    $array = $message->toArray();
                    if ($isShort) {
                        $array['file'] = \str_replace($path, '', $array['file']);
                    }

                    return $array;
                },
                \iterator_to_array($result->getMessage())
            )
        ];
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array $result
     */
    private function renderTable(OutputInterface $output, array $result): void
    {
        $messagesTable = new Table($output);
        $messagesTable->setHeaders(['Type', 'Rule', 'File', 'Message']);
        $stats = [];
        foreach ($result['messages'] as $message) {
            $type = $message['type'];
            if (!isset($stats[$type])) {
                $stats[$type] = 0;
            }
            $stats[$type]++;
            $messagesTable->addRow(
                [
                    $this->decorate($message, $message['type']),
                    $this->decorate($message, $message['rule']),
                    $this->decorate($message, $message['file'] . ':' . $message['line']),
                    $this->decorate($message, $message['content'])
                ]
            );
        }

        $messagesTable->render();
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array $result
     */
    private function renderJson(OutputInterface $output, array $result): void
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

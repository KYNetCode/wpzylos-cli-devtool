<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Test Command.
 *
 * Creates a new PHPUnit test class.
 *
 * Usage:
 *   wpzylos make:test UserServiceTest
 *   wpzylos make:test Feature/OrderProcessingTest --unit
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeTestCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Test';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:test')
            ->setDescription('Create a new PHPUnit test class')
            ->setHelp(
                'This command creates a new PHPUnit test class.' . PHP_EOL .
                'Tests are placed in tests/Unit by default, or tests/Feature with --feature.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:test UserServiceTest</comment>' . PHP_EOL .
                '  <comment>wpzylos make:test Feature/OrderTest --feature</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Test class name (e.g., UserServiceTest)')
            ->addOption('feature', null, InputOption::VALUE_NONE, 'Create a feature test (in tests/Feature)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'test';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Tests\\Unit';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'tests/Unit';
    }

    /** @inheritDoc */
    protected function getQualifiedClassName(array $nameData): string
    {
        // Tests use a different namespace root
        $namespace = $this->getDefaultNamespaceSegment();

        if (!empty($nameData['path'])) {
            $namespace .= '\\' . $nameData['path'];
        }

        return $namespace . '\\' . $nameData['class'];
    }

    /** @inheritDoc */
    protected function getFilePath(array $nameData): string
    {
        $path = $this->getPluginPath();
        $directory = rtrim($path, '/\\') . '/' . $this->getDefaultOutputDirectory();

        if (!empty($nameData['path'])) {
            $directory .= '/' . str_replace('\\', '/', $nameData['path']);
        }

        return $directory . '/' . $nameData['class'] . '.php';
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $type = $input->getOption('feature') ? 'Feature' : 'Unit';

        $output->writeln('');
        $output->writeln('<info>Test Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Type:</comment>      ' . $type);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Run tests:</info>');
        $output->writeln('  <comment>php vendor/bin/phpunit --filter=' . $nameData['class'] . '</comment>');
    }
}

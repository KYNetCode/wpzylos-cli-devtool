<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Observer Command.
 *
 * Creates a new model lifecycle observer with hooks for creating,
 * created, updating, updated, deleting, and deleted events.
 *
 * Usage:
 *   wpzylos make:observer UserObserver
 *   wpzylos make:observer OrderObserver --model=Order
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeObserverCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Observer';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:observer')
            ->setDescription('Create a new model lifecycle observer')
            ->setHelp(
                'This command creates a model lifecycle observer with hooks' . PHP_EOL .
                'for creating/created, updating/updated, deleting/deleted events.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:observer UserObserver</comment>' . PHP_EOL .
                '  <comment>wpzylos make:observer OrderObserver --model=Order</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Observer class name (e.g., UserObserver)')
            ->addOption('model', 'm', InputOption::VALUE_OPTIONAL, 'The model class to observe');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'observer';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Observers';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Observers';
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $rootNamespace = $this->context['namespace'] ?? 'MyPlugin';
        $modelName = $input->getOption('model')
            ?: str_replace('Observer', '', $nameData['class']);

        $output->writeln('');
        $output->writeln('<info>Observer Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Model:</comment>     ' . $rootNamespace . '\\Models\\' . $modelName);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Register in your service provider:</info>');
        $output->writeln('  <comment>$model->observe(new \\' . $qualifiedName . '());</comment>');
    }
}

<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Factory Command.
 *
 * Creates a new model factory for generating test data.
 *
 * Usage:
 *   wpzylos make:factory UserFactory
 *   wpzylos make:factory OrderFactory --model=Order
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeFactoryCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Factory';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:factory')
            ->setDescription('Create a new model factory for testing')
            ->setHelp(
                'This command creates a model factory for generating test data.' . PHP_EOL .
                'Factories define default attributes and support batch creation.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:factory UserFactory</comment>' . PHP_EOL .
                '  <comment>wpzylos make:factory OrderFactory --model=Order</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Factory class name (e.g., UserFactory)')
            ->addOption('model', 'm', InputOption::VALUE_OPTIONAL, 'The model class this factory creates');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'factory';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Database\\Factories';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'database/factories';
    }

    /** @inheritDoc */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $rootNamespace = $this->context['namespace'] ?? 'MyPlugin';
        $modelName = $input->getOption('model')
            ?: str_replace('Factory', '', $nameData['class']);
        $modelClass = $rootNamespace . '\\Models\\' . $modelName;

        return [
            'modelClass' => $modelClass,
        ];
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
            ?: str_replace('Factory', '', $nameData['class']);

        $output->writeln('');
        $output->writeln('<info>Factory Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Model:</comment>     ' . $rootNamespace . '\\Models\\' . $modelName);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage in tests:</info>');
        $output->writeln('  <comment>$factory = new \\' . $qualifiedName . '();</comment>');
        $output->writeln('  <comment>$data = $factory->make();</comment>');
        $output->writeln('  <comment>$items = $factory->makeMany(5);</comment>');
    }
}

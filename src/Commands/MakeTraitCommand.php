<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Trait Command.
 *
 * Creates a new PHP trait for shared behavior across classes.
 *
 * Usage:
 *   wpzylos make:trait HasSlug
 *   wpzylos make:trait Concerns/HasTimestamps
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeTraitCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Trait';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:trait')
            ->setDescription('Create a new PHP trait')
            ->setHelp(
                'This command creates a new PHP trait for encapsulating reusable behavior.' . PHP_EOL .
                'Traits are ideal for sharing methods across unrelated classes.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:trait HasSlug</comment>' . PHP_EOL .
                '  <comment>wpzylos make:trait Concerns/HasTimestamps</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Trait name (e.g., HasSlug)');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'trait';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Traits';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Traits';
    }

    /**
     * @inheritDoc
     */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln('');
        $output->writeln('<info>Trait Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  Use this trait in any class:');
        $output->writeln('  <comment>use \\' . $qualifiedName . ';</comment>');
    }
}

<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Scope Command.
 *
 * Creates a reusable query scope class for model queries.
 *
 * Usage:
 *   wpzylos make:scope ActiveScope
 *   wpzylos make:scope Scopes/PublishedScope
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeScopeCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Scope';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:scope')
            ->setDescription('Create a new reusable query scope')
            ->setHelp(
                'This command creates a reusable query scope class.' . PHP_EOL .
                'Scopes encapsulate common query constraints for reuse.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:scope ActiveScope</comment>' . PHP_EOL .
                '  <comment>wpzylos make:scope Scopes/PublishedScope</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Scope class name (e.g., ActiveScope)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'scope';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Models\\Scopes';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Models/Scopes';
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln('');
        $output->writeln('<info>Scope Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  <comment>$query = \\' . $qualifiedName . '::scope($query);</comment>');
    }
}

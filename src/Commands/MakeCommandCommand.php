<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Command Command.
 *
 * Creates a new custom CLI command class extending Symfony Console.
 *
 * Usage:
 *   wpzylos make:command PurgeCache
 *   wpzylos make:command Billing/SyncSubscriptions
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeCommandCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Command';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:command')
            ->setDescription('Create a new custom CLI command')
            ->setHelp(
                'This command creates a new Symfony Console command class.' . PHP_EOL .
                'Register it in your bin/wpzylos executable to make it available.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:command PurgeCache</comment>' . PHP_EOL .
                '  <comment>wpzylos make:command Billing/SyncSubscriptions</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Command class name (e.g., PurgeCache)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'command';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Console\\Commands';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Console/Commands';
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
        $output->writeln('<info>Command Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Next steps:</info>');
        $output->writeln('  1. Update the command name in <comment>configure()</comment>');
        $output->writeln('  2. Add arguments and options');
        $output->writeln('  3. Register in <comment>bin/wpzylos</comment>:');
        $output->writeln('     <comment>$application->add(new \\' . $qualifiedName . '());</comment>');
    }
}

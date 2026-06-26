<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Interface Command.
 *
 * Creates a new PHP interface for contract-driven design.
 *
 * Usage:
 *   wpzylos make:interface PaymentGateway
 *   wpzylos make:interface Contracts/Searchable
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeInterfaceCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Interface';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:interface')
            ->setDescription('Create a new PHP interface')
            ->setHelp(
                'This command creates a new PHP interface for contract-driven design.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:interface PaymentGateway</comment>' . PHP_EOL .
                '  <comment>wpzylos make:interface Contracts/Searchable</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Interface name (e.g., PaymentGateway)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'interface';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Contracts';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Contracts';
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
        $output->writeln('<info>Interface Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  <comment>class MyService implements \\' . $qualifiedName . ' { ... }</comment>');
    }
}

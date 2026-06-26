<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Enum Command.
 *
 * Creates a new PHP 8.1 enum (backed or plain).
 *
 * Usage:
 *   wpzylos make:enum Status                  (string-backed, default)
 *   wpzylos make:enum Priority --int          (int-backed)
 *   wpzylos make:enum Color --plain           (unit enum, no backing type)
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeEnumCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Enum';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:enum')
            ->setDescription('Create a new PHP 8.1 enum')
            ->setHelp(
                'This command creates a new PHP 8.1 enum. By default, a string-backed' . PHP_EOL .
                'enum is generated. Use --int for integer backing or --plain for a unit enum.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:enum Status</comment>              (string-backed)' . PHP_EOL .
                '  <comment>wpzylos make:enum Priority --int</comment>      (int-backed)' . PHP_EOL .
                '  <comment>wpzylos make:enum Color --plain</comment>       (unit enum)'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Enum name (e.g., Status)')
            ->addOption('int', null, InputOption::VALUE_NONE, 'Create an int-backed enum')
            ->addOption('plain', null, InputOption::VALUE_NONE, 'Create a plain (unit) enum without backing type');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        if ($input->getOption('plain')) {
            return 'enum.plain';
        }

        return 'enum';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Enums';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Enums';
    }

    /**
     * @inheritDoc
     */
    protected function buildClass(string $qualifiedName, array $nameData, InputInterface $input): string
    {
        $content = parent::buildClass($qualifiedName, $nameData, $input);

        // Replace string backing type with int if --int flag is used
        if ($input->getOption('int') && !$input->getOption('plain')) {
            $content = str_replace(
                'enum ' . $nameData['class'] . ': string',
                'enum ' . $nameData['class'] . ': int',
                $content
            );
        }

        return $content;
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
        $backingType = 'string';
        if ($input->getOption('plain')) {
            $backingType = 'unit (no backing type)';
        } elseif ($input->getOption('int')) {
            $backingType = 'int';
        }

        $output->writeln('');
        $output->writeln('<info>Enum Details:</info>');
        $output->writeln('  <comment>Class:</comment>       ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>   ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Backing Type:</comment> ' . $backingType);
        $output->writeln('  <comment>File:</comment>        ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  <comment>\\' . $qualifiedName . '::Example</comment>');
        if (!$input->getOption('plain')) {
            $output->writeln('  <comment>\\' . $qualifiedName . '::Example->value</comment>');
            $output->writeln('  <comment>\\' . $qualifiedName . '::from(\'example\')</comment>');
            $output->writeln('  <comment>\\' . $qualifiedName . '::tryFrom(\'example\')</comment>');
        }
    }
}

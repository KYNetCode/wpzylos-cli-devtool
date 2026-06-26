<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Rule Command.
 *
 * Creates a new validation rule class with passes() and message() methods.
 *
 * Usage:
 *   wpzylos make:rule Uppercase
 *   wpzylos make:rule Validators/StrongPassword
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeRuleCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Rule';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-validation'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:rule')
            ->setDescription('Create a new validation rule class')
            ->setHelp(
                'This command creates a custom validation rule class with passes()' . PHP_EOL .
                'and message() methods. Requires the wpzylos-validation package.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:rule Uppercase</comment>' . PHP_EOL .
                '  <comment>wpzylos make:rule Validators/StrongPassword</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Validation rule class name (e.g., Uppercase)');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'rule';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Rules';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Rules';
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
        $output->writeln('<info>Rule Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  Use this rule in validation:');
        $output->writeln('  <comment>$validator->addRule(\'field\', new \\' . $qualifiedName . '());</comment>');
    }
}

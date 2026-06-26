<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Policy Command.
 *
 * Creates an authorization policy class using WordPress capabilities
 * (current_user_can) for CRUD permission checks.
 *
 * Usage:
 *   wpzylos make:policy PostPolicy
 *   wpzylos make:policy Policies/OrderPolicy
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakePolicyCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Policy';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:policy')
            ->setDescription('Create a new authorization policy')
            ->setHelp(
                'This command creates an authorization policy class using' . PHP_EOL .
                'WordPress capabilities (current_user_can) for CRUD checks.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:policy PostPolicy</comment>' . PHP_EOL .
                '  <comment>wpzylos make:policy Policies/OrderPolicy</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Policy class name (e.g., PostPolicy)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'policy';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Policies';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Policies';
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
        $output->writeln('<info>Policy Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  <comment>$policy = new \\' . $qualifiedName . '();</comment>');
        $output->writeln('  <comment>if ($policy->update($user, $model)) { ... }</comment>');

        $output->writeln('');
        $output->writeln('<info>Available methods:</info>');
        $output->writeln('  viewAny, view, create, update, delete');
    }
}

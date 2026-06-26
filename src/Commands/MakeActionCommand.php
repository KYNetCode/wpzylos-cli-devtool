<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Action Command.
 *
 * Creates a new WordPress action hook handler class with a handle() method
 * and self-registering boot() for attaching to WordPress hooks.
 *
 * Usage:
 *   wpzylos make:action SendWelcomeEmail --hook=user_register
 *   wpzylos make:action CleanupExpiredTokens --hook=wp_scheduled_delete --priority=20
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeActionCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Action';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:action')
            ->setDescription('Create a new WordPress action hook handler')
            ->setHelp(
                'This command creates a dedicated class for handling a WordPress action hook.' . PHP_EOL .
                'The handler pattern keeps your hook logic organized and testable.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:action SendWelcomeEmail --hook=user_register</comment>' . PHP_EOL .
                '  <comment>wpzylos make:action CleanupExpiredTokens --hook=wp_scheduled_delete --priority=20</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Action handler class name')
            ->addOption('hook', null, InputOption::VALUE_OPTIONAL, 'WordPress action hook name', 'init')
            ->addOption('priority', null, InputOption::VALUE_OPTIONAL, 'Hook priority', '10');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'action';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Actions';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Actions';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [
            'hook'     => $input->getOption('hook') ?: 'init',
            'priority' => $input->getOption('priority') ?: '10',
        ];
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
        $hook = $input->getOption('hook') ?: 'init';
        $priority = $input->getOption('priority') ?: '10';

        $output->writeln('');
        $output->writeln('<info>Action Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Hook:</comment>      ' . $hook);
        $output->writeln('  <comment>Priority:</comment>  ' . $priority);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');
    }
}

<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Cron Command.
 *
 * Creates a new WordPress cron/scheduled task handler.
 *
 * Usage:
 *   wpzylos make:cron CleanupExpiredTokens
 *   wpzylos make:cron SyncInventory --recurrence=hourly
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeCronCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Cron';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:cron')
            ->setDescription('Create a new WordPress cron/scheduled task')
            ->setHelp(
                'This command creates a WordPress cron handler with schedule/unschedule' . PHP_EOL .
                'lifecycle and plugin activation/deactivation hooks.' . PHP_EOL .
                PHP_EOL .
                'Recurrence options: hourly, twicedaily, daily, weekly' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:cron CleanupExpiredTokens</comment>' . PHP_EOL .
                '  <comment>wpzylos make:cron SyncInventory --recurrence=hourly</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Cron handler class name')
            ->addOption('recurrence', 'r', InputOption::VALUE_OPTIONAL, 'Recurrence interval (hourly, twicedaily, daily, weekly)', 'daily');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'cron';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Console\\Cron';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Console/Cron';
    }

    /** @inheritDoc */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [
            'recurrence' => $input->getOption('recurrence') ?: 'daily',
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
        $prefix = $this->context['prefix'] ?? 'mp_';
        $recurrence = $input->getOption('recurrence') ?: 'daily';

        $output->writeln('');
        $output->writeln('<info>Cron Details:</info>');
        $output->writeln('  <comment>Class:</comment>      ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>  ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Hook:</comment>       ' . $prefix . $nameData['snake']);
        $output->writeln('  <comment>Recurrence:</comment> ' . $recurrence);
        $output->writeln('  <comment>File:</comment>       ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');

        $output->writeln('');
        $output->writeln('<info>Deactivation hook:</info>');
        $output->writeln('  <comment>register_deactivation_hook(__FILE__, [\\' . $qualifiedName . '::class, \'deactivate\']);</comment>');
    }
}

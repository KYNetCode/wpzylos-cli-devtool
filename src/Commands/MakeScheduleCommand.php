<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Schedule Command.
 *
 * Creates a new scheduled task class for the WPZylos Scheduler.
 * Tasks define their frequency using the fluent Frequencies trait API
 * and are registered via the SchedulerServiceProvider.
 *
 * Usage:
 *   wpzylos make:schedule CleanupExpiredTokens
 *   wpzylos make:schedule SyncInventory --frequency=hourly
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeScheduleCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Schedule';

    /** @inheritDoc */
    protected array $requiredPackages = ['wpzylos-scheduler'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:schedule')
            ->setDescription('Create a new scheduled task class')
            ->setHelp(
                'This command creates a scheduled task for the WPZylos Scheduler.' . PHP_EOL .
                'Tasks use the Schedule fluent API with methods like daily(), hourly(), weekly().' . PHP_EOL .
                PHP_EOL .
                'Frequency options: everyMinute, everyFiveMinutes, everyTenMinutes,' . PHP_EOL .
                '  everyFifteenMinutes, everyThirtyMinutes, hourly, daily, weekly, monthly' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:schedule CleanupExpiredTokens</comment>' . PHP_EOL .
                '  <comment>wpzylos make:schedule SyncInventory --frequency=hourly</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Scheduled task class name')
            ->addOption('frequency', null, InputOption::VALUE_OPTIONAL, 'Default frequency (daily, hourly, weekly, monthly)', 'daily');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'schedule';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Console\\Schedules';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Console/Schedules';
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $frequency = $input->getOption('frequency') ?: 'daily';

        $output->writeln('');
        $output->writeln('<info>Schedule Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Frequency:</comment> ' . $frequency);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Registration:</info>');
        $output->writeln('  Add to your SchedulerServiceProvider::schedule() method:');
        $output->writeln('  <comment>$schedule->call(new \\' . $qualifiedName . '())->' . $frequency . '();</comment>');

        $output->writeln('');
        $output->writeln('<info>Or use the invokable pattern:</info>');
        $output->writeln('  <comment>(new \\' . $qualifiedName . '())($schedule);</comment>');
    }
}

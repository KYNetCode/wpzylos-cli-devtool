<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Event Command.
 *
 * Creates a new event class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeEventCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Event';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-events'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:event')
            ->setDescription('Create a new event class')
            ->addArgument('name', InputArgument::REQUIRED, 'Event class name');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'event';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Events';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Events';
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
        $output->writeln('<info>Event Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  Dispatch this event:');
        $output->writeln('  <comment>$dispatcher->dispatch(new \\' . $qualifiedName . '($data));</comment>');

        $output->writeln('');
        $output->writeln('  Create a listener:');
        $output->writeln('  <comment>wpzylos make:listener ' . $nameData['class'] . 'Listener --event=' . $nameData['class'] . '</comment>');
    }
}

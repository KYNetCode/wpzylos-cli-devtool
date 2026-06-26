<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Listener Command.
 *
 * Creates a new event listener class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeListenerCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Listener';

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
            ->setName('make:listener')
            ->setDescription('Create a new event listener class')
            ->addArgument('name', InputArgument::REQUIRED, 'Listener class name')
            ->addOption('event', 'e', InputOption::VALUE_OPTIONAL, 'Event class to listen to')
            ->addOption('queued', 'q', InputOption::VALUE_NONE, 'Create a queued listener');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'listener';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Listeners';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Listeners';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $event = $input->getOption('event');
        $rootNamespace = $this->context['namespace'] ?? 'MyPlugin';

        // If event is just a class name, assume it's in the Events namespace
        if ($event && !str_contains($event, '\\')) {
            $eventNamespace = $rootNamespace . '\\Events\\' . $event;
            $eventClass = $event;
        } elseif ($event) {
            $eventNamespace = $event;
            $parts = explode('\\', $event);
            $eventClass = end($parts);
        } else {
            $eventNamespace = $rootNamespace . '\\Events\\ExampleEvent';
            $eventClass = 'ExampleEvent';
        }

        return [
            'event' => $eventClass,
            'eventNamespace' => $eventNamespace,
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
        $event = $input->getOption('event') ?: 'ExampleEvent';

        $output->writeln('');
        $output->writeln('<info>Listener Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Event:</comment>     ' . $event);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Next steps:</info>');
        $output->writeln('  Register this listener in your EventServiceProvider or config/events.php:');
        $output->writeln('  <comment>' . $event . '::class => [\\' . $qualifiedName . '::class],</comment>');
    }
}

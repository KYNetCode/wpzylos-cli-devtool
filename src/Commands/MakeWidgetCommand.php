<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Widget Command.
 *
 * Creates a new WordPress widget class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeWidgetCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Widget';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:widget')
            ->setDescription('Create a new WordPress widget class')
            ->addArgument('name', InputArgument::REQUIRED, 'Widget class name')
            ->addOption('widget-id', null, InputOption::VALUE_OPTIONAL, 'Widget ID')
            ->addOption('widget-title', null, InputOption::VALUE_OPTIONAL, 'Widget title')
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Widget description', 'A custom widget.');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'widget';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Widgets';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Widgets';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [
            'widgetId' => $input->getOption('widget-id') ?: $this->snake($nameData['class']) . '_widget',
            'widgetTitle' => $input->getOption('widget-title') ?: $nameData['title'] . ' Widget',
            'widgetDescription' => $input->getOption('description') ?: 'A custom widget.',
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
        $widgetId = $input->getOption('widget-id') ?: $this->snake($nameData['class']) . '_widget';
        $widgetTitle = $input->getOption('widget-title') ?: $nameData['title'] . ' Widget';
        $description = $input->getOption('description') ?: 'A custom widget.';

        $output->writeln('');
        $output->writeln('<info>Widget Details:</info>');
        $output->writeln('  <comment>Class:</comment>       ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>   ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>ID:</comment>          ' . $widgetId);
        $output->writeln('  <comment>Title:</comment>       ' . $widgetTitle);
        $output->writeln('  <comment>Description:</comment> ' . $description);
        $output->writeln('  <comment>File:</comment>        ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');
    }
}

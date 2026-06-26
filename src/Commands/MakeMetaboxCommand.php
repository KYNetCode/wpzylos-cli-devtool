<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Metabox Command.
 *
 * Creates a new WordPress metabox class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeMetaboxCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Metabox';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:metabox')
            ->setDescription('Create a new WordPress metabox class')
            ->addArgument('name', InputArgument::REQUIRED, 'Metabox class name')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Metabox ID')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Metabox title')
            ->addOption('screen', null, InputOption::VALUE_OPTIONAL, 'Post types to show on (comma-separated)', 'post')
            ->addOption('context', null, InputOption::VALUE_OPTIONAL, 'Context (normal, side, advanced)', 'normal')
            ->addOption('priority', null, InputOption::VALUE_OPTIONAL, 'Priority (high, core, default, low)', 'default');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'metabox';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Metaboxes';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Metaboxes';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $id = $input->getOption('id') ?: $this->snake($nameData['class']) . '_metabox';
        $title = $input->getOption('title') ?: $nameData['title'];

        // Build screen array or null string
        $screenStr = $input->getOption('screen') ?: 'post';
        $screenArr = array_map('trim', explode(',', $screenStr));
        $screen = count($screenArr) === 1
            ? "'" . $screenArr[0] . "'"
            : "['" . implode("', '", $screenArr) . "']";

        return [
            'id' => $id,
            'metaboxTitle' => $title,
            'screen' => $screen,
            'context' => $input->getOption('context') ?: 'normal',
            'priority' => $input->getOption('priority') ?: 'default',
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
        $id = $input->getOption('id') ?: $this->snake($nameData['class']) . '_metabox';
        $title = $input->getOption('title') ?: $nameData['title'];
        $screen = $input->getOption('screen') ?: 'post';
        $context = $input->getOption('context') ?: 'normal';
        $priority = $input->getOption('priority') ?: 'default';

        $output->writeln('');
        $output->writeln('<info>Metabox Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>ID:</comment>        ' . $id);
        $output->writeln('  <comment>Title:</comment>     ' . $title);
        $output->writeln('  <comment>Screen:</comment>    ' . $screen);
        $output->writeln('  <comment>Context:</comment>   ' . $context);
        $output->writeln('  <comment>Priority:</comment>  ' . $priority);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');
    }
}

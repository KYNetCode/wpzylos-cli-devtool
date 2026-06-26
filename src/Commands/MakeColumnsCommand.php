<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Columns Command.
 *
 * Creates a new admin list table columns handler class for customizing
 * the WordPress admin post list columns.
 *
 * Usage:
 *   wpzylos make:columns ProductColumns --post-type=product
 *   wpzylos make:columns OrderColumns
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeColumnsCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Columns';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:columns')
            ->setDescription('Create a new admin list table columns handler')
            ->setHelp(
                'This command creates a columns handler for customizing WordPress admin' . PHP_EOL .
                'list table columns. The generated class includes methods for registering,' . PHP_EOL .
                'rendering, and making columns sortable.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:columns ProductColumns --post-type=product</comment>' . PHP_EOL .
                '  <comment>wpzylos make:columns OrderColumns</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Columns handler class name')
            ->addOption('post-type', null, InputOption::VALUE_OPTIONAL, 'Post type to add columns to', 'post');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'columns';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Columns';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Columns';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [
            'postType' => $input->getOption('post-type') ?: 'post',
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
        $postType = $input->getOption('post-type') ?: 'post';

        $output->writeln('');
        $output->writeln('<info>Columns Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Post Type:</comment> ' . $postType);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');
    }
}

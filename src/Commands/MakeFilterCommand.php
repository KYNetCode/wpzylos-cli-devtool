<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Filter Command.
 *
 * Creates a new WordPress filter hook handler class with a handle() method
 * that receives and returns the filtered value, and a self-registering boot().
 *
 * Usage:
 *   wpzylos make:filter ModifyExcerptLength --hook=excerpt_length
 *   wpzylos make:filter CustomLoginUrl --hook=login_url --priority=99
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeFilterCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Filter';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:filter')
            ->setDescription('Create a new WordPress filter hook handler')
            ->setHelp(
                'This command creates a dedicated class for handling a WordPress filter hook.' . PHP_EOL .
                'The handler must return the modified value to pass it along the filter chain.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:filter ModifyExcerptLength --hook=excerpt_length</comment>' . PHP_EOL .
                '  <comment>wpzylos make:filter CustomLoginUrl --hook=login_url --priority=99</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Filter handler class name')
            ->addOption('hook', null, InputOption::VALUE_OPTIONAL, 'WordPress filter hook name', 'the_content')
            ->addOption('priority', null, InputOption::VALUE_OPTIONAL, 'Hook priority', '10');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'filter';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Filters';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Filters';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [
            'hook'     => $input->getOption('hook') ?: 'the_content',
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
        $hook = $input->getOption('hook') ?: 'the_content';
        $priority = $input->getOption('priority') ?: '10';

        $output->writeln('');
        $output->writeln('<info>Filter Details:</info>');
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

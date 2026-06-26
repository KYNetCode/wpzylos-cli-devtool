<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Ajax Command.
 *
 * Creates a new WordPress AJAX handler class with nonce verification,
 * authorization, and wp_ajax hooks.
 *
 * Usage:
 *   wpzylos make:ajax LoadMorePosts
 *   wpzylos make:ajax SearchProducts --public
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeAjaxCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Ajax Handler';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:ajax')
            ->setDescription('Create a new WordPress AJAX handler')
            ->setHelp(
                'This command creates a WordPress AJAX handler with nonce verification,' . PHP_EOL .
                'authorization checks, and both wp_ajax_ and wp_ajax_nopriv_ hooks.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:ajax LoadMorePosts</comment>' . PHP_EOL .
                '  <comment>wpzylos make:ajax SearchProducts --public</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'AJAX handler class name')
            ->addOption('public', null, InputOption::VALUE_NONE, 'Allow non-logged-in users');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'ajax';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Http\\Ajax';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Http/Ajax';
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
        $action = $prefix . $nameData['snake'];

        $output->writeln('');
        $output->writeln('<info>Ajax Handler Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Action:</comment>    ' . $action);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');

        $output->writeln('');
        $output->writeln('<info>Frontend usage:</info>');
        $output->writeln('  <comment>jQuery.post(ajaxurl, { action: \'' . $action . '\', nonce: \'...\' });</comment>');
    }
}

<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Post Type Command.
 *
 * Creates a new WordPress custom post type class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakePostTypeCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'PostType';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:posttype')
            ->setDescription('Create a new WordPress custom post type class')
            ->addArgument('name', InputArgument::REQUIRED, 'Post type class name')
            ->addOption('cpt-slug', null, InputOption::VALUE_OPTIONAL, 'Post type slug (max 20 chars)')
            ->addOption('singular', null, InputOption::VALUE_OPTIONAL, 'Singular label')
            ->addOption('plural', null, InputOption::VALUE_OPTIONAL, 'Plural label')
            ->addOption('icon', null, InputOption::VALUE_OPTIONAL, 'Menu icon (dashicons)', 'dashicons-admin-post')
            ->addOption('supports', null, InputOption::VALUE_OPTIONAL, 'Supported features (comma-separated)', 'title,editor,thumbnail')
            ->addOption('public', null, InputOption::VALUE_NONE, 'Make post type public (default: true)')
            ->addOption('no-public', null, InputOption::VALUE_NONE, 'Make post type private')
            ->addOption('has-archive', null, InputOption::VALUE_NONE, 'Enable archives (default: true)')
            ->addOption('no-archive', null, InputOption::VALUE_NONE, 'Disable archives')
            ->addOption('hierarchical', null, InputOption::VALUE_NONE, 'Make hierarchical like pages')
            ->addOption('show-in-rest', null, InputOption::VALUE_NONE, 'Enable REST API and Gutenberg (default: true)')
            ->addOption('no-rest', null, InputOption::VALUE_NONE, 'Disable REST API')
            ->addOption('menu-position', null, InputOption::VALUE_OPTIONAL, 'Menu position', '25');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'posttype';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\PostTypes';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/PostTypes';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $cptSlug = $input->getOption('cpt-slug') ?: $this->kebab($nameData['class']);

        // Ensure slug is max 20 characters
        if (strlen($cptSlug) > 20) {
            $cptSlug = substr($cptSlug, 0, 20);
        }

        $singular = $input->getOption('singular') ?: $nameData['title'];
        $plural = $input->getOption('plural') ?: $this->plural($nameData['title']);

        // Build supports array string
        $supportsStr = $input->getOption('supports') ?: 'title,editor,thumbnail';
        $supportsArr = array_map('trim', explode(',', $supportsStr));
        $supports = "['" . implode("', '", $supportsArr) . "']";

        // Determine boolean flags with defaults
        $public = !$input->getOption('no-public');
        $hasArchive = !$input->getOption('no-archive');
        $showInRest = !$input->getOption('no-rest');
        $hierarchical = $input->getOption('hierarchical');

        return [
            'slug' => $cptSlug,
            'singular' => $singular,
            'plural' => $plural,
            'icon' => $input->getOption('icon') ?: 'dashicons-admin-post',
            'supports' => $supports,
            'public' => $public ? 'true' : 'false',
            'publiclyQueryable' => $public ? 'true' : 'false',
            'hasArchive' => $hasArchive ? 'true' : 'false',
            'showInRest' => $showInRest ? 'true' : 'false',
            'hierarchical' => $hierarchical ? 'true' : 'false',
            'menuPosition' => $input->getOption('menu-position') ?: '25',
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
        $cptSlug = $input->getOption('cpt-slug') ?: $this->kebab($nameData['class']);
        $singular = $input->getOption('singular') ?: $nameData['title'];
        $plural = $input->getOption('plural') ?: $this->plural($nameData['title']);

        $output->writeln('');
        $output->writeln('<info>Post Type Details:</info>');
        $output->writeln('  <comment>Class:</comment>      ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>  ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Slug:</comment>       ' . $cptSlug);
        $output->writeln('  <comment>Singular:</comment>   ' . $singular);
        $output->writeln('  <comment>Plural:</comment>     ' . $plural);
        $output->writeln('  <comment>File:</comment>       ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');

        $output->writeln('');
        $output->writeln('<info>Auto-discovery:</info>');
        $output->writeln('  If auto-discovery is enabled, this post type will register automatically.');
    }
}

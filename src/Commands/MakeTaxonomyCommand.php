<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Taxonomy Command.
 *
 * Creates a new WordPress custom taxonomy class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeTaxonomyCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Taxonomy';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:taxonomy')
            ->setDescription('Create a new WordPress custom taxonomy class')
            ->addArgument('name', InputArgument::REQUIRED, 'Taxonomy class name')
            ->addOption('tax-slug', null, InputOption::VALUE_OPTIONAL, 'Taxonomy slug')
            ->addOption('singular', null, InputOption::VALUE_OPTIONAL, 'Singular label')
            ->addOption('plural', null, InputOption::VALUE_OPTIONAL, 'Plural label')
            ->addOption('post-types', null, InputOption::VALUE_OPTIONAL, 'Post types to attach (comma-separated)', 'post')
            ->addOption('hierarchical', null, InputOption::VALUE_NONE, 'Make hierarchical like categories')
            ->addOption('tag', null, InputOption::VALUE_NONE, 'Non-hierarchical like tags (default)')
            ->addOption('show-admin-column', null, InputOption::VALUE_NONE, 'Show in admin column (default: true)')
            ->addOption('no-admin-column', null, InputOption::VALUE_NONE, 'Hide admin column')
            ->addOption('show-in-rest', null, InputOption::VALUE_NONE, 'Enable REST API (default: true)')
            ->addOption('no-rest', null, InputOption::VALUE_NONE, 'Disable REST API');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'taxonomy';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Taxonomies';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Taxonomies';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $taxSlug = $input->getOption('tax-slug') ?: $this->kebab($nameData['class']);
        $singular = $input->getOption('singular') ?: $nameData['title'];
        $plural = $input->getOption('plural') ?: $this->plural($nameData['title']);

        // Build post types array string
        $postTypesStr = $input->getOption('post-types') ?: 'post';
        $postTypesArr = array_map('trim', explode(',', $postTypesStr));
        $postTypes = "['" . implode("', '", $postTypesArr) . "']";

        // Determine boolean flags with defaults
        $hierarchical = $input->getOption('hierarchical') && !$input->getOption('tag');
        $showAdminColumn = !$input->getOption('no-admin-column');
        $showInRest = !$input->getOption('no-rest');

        return [
            'slug' => $taxSlug,
            'singular' => $singular,
            'plural' => $plural,
            'postTypes' => $postTypes,
            'hierarchical' => $hierarchical ? 'true' : 'false',
            'showAdminColumn' => $showAdminColumn ? 'true' : 'false',
            'showInRest' => $showInRest ? 'true' : 'false',
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
        $taxSlug = $input->getOption('tax-slug') ?: $this->kebab($nameData['class']);
        $singular = $input->getOption('singular') ?: $nameData['title'];
        $plural = $input->getOption('plural') ?: $this->plural($nameData['title']);
        $postTypes = $input->getOption('post-types') ?: 'post';
        $hierarchical = $input->getOption('hierarchical') && !$input->getOption('tag');

        $output->writeln('');
        $output->writeln('<info>Taxonomy Details:</info>');
        $output->writeln('  <comment>Class:</comment>        ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>    ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Slug:</comment>         ' . $taxSlug);
        $output->writeln('  <comment>Singular:</comment>     ' . $singular);
        $output->writeln('  <comment>Plural:</comment>       ' . $plural);
        $output->writeln('  <comment>Post Types:</comment>   ' . $postTypes);
        $output->writeln('  <comment>Hierarchical:</comment> ' . ($hierarchical ? 'Yes (like categories)' : 'No (like tags)'));
        $output->writeln('  <comment>File:</comment>         ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');
    }
}

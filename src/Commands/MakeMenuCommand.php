<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Menu Command.
 *
 * Creates a new WordPress admin menu class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeMenuCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Menu';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:menu')
            ->setDescription('Create a new WordPress admin menu class')
            ->addArgument('name', InputArgument::REQUIRED, 'Menu class name')
            ->addOption('menu-slug', null, InputOption::VALUE_OPTIONAL, 'Menu slug')
            ->addOption('page-title', null, InputOption::VALUE_OPTIONAL, 'Page title')
            ->addOption('menu-title', null, InputOption::VALUE_OPTIONAL, 'Menu title')
            ->addOption('icon', null, InputOption::VALUE_OPTIONAL, 'Menu icon', 'dashicons-admin-generic')
            ->addOption('icon-type', null, InputOption::VALUE_OPTIONAL, 'Icon type: auto, dashicon, asset, url, data, none', 'auto')
            ->addOption('position', null, InputOption::VALUE_OPTIONAL, 'Menu position', '30')
            ->addOption('capability', null, InputOption::VALUE_OPTIONAL, 'Required capability', 'manage_options')
            ->addOption('vite-entry', null, InputOption::VALUE_OPTIONAL, 'Optional Vite entry to enqueue on this menu page', '')
            ->addOption('asset-handle', null, InputOption::VALUE_OPTIONAL, 'Asset handle for the optional Vite entry')
            ->addOption('mount-id', null, InputOption::VALUE_OPTIONAL, 'DOM mount element ID');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'menu';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Admin\\Menus';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Admin/Menus';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $menuSlug = $input->getOption('menu-slug') ?: $this->kebab($nameData['class']);
        $position = trim((string) ($input->getOption('position') ?: '30'));
        $assetHandle = $input->getOption('asset-handle') ?: ($menuSlug . '-admin');
        $mountId = $input->getOption('mount-id') ?: ($menuSlug . '-app');

        return [
            'menuSlug' => $menuSlug,
            'pageTitle' => $input->getOption('page-title') ?: $nameData['title'],
            'menuTitle' => $input->getOption('menu-title') ?: $nameData['title'],
            'icon' => $input->getOption('icon') ?: 'dashicons-admin-generic',
            'iconType' => $input->getOption('icon-type') ?: 'auto',
            'position' => $position === '' || strtolower($position) === 'null' ? 'null' : $position,
            'capability' => $input->getOption('capability') ?: 'manage_options',
            'viteEntry' => $input->getOption('vite-entry') ?: '',
            'assetHandle' => $assetHandle,
            'mountId' => $mountId,
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
        $menuSlug = $input->getOption('menu-slug') ?: $this->kebab($nameData['class']);
        $pageTitle = $input->getOption('page-title') ?: $nameData['title'];
        $menuTitle = $input->getOption('menu-title') ?: $nameData['title'];
        $icon = $input->getOption('icon') ?: 'dashicons-admin-generic';
        $iconType = $input->getOption('icon-type') ?: 'auto';
        $position = $input->getOption('position') ?: '30';
        $capability = $input->getOption('capability') ?: 'manage_options';
        $viteEntry = $input->getOption('vite-entry') ?: '';
        $assetHandle = $input->getOption('asset-handle') ?: ($menuSlug . '-admin');
        $mountId = $input->getOption('mount-id') ?: ($menuSlug . '-app');

        $output->writeln('');
        $output->writeln('<info>Menu Details:</info>');
        $output->writeln('  <comment>Class:</comment>      ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>  ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Slug:</comment>       ' . $menuSlug);
        $output->writeln('  <comment>Page Title:</comment> ' . $pageTitle);
        $output->writeln('  <comment>Menu Title:</comment> ' . $menuTitle);
        $output->writeln('  <comment>Icon:</comment>       ' . $icon);
        $output->writeln('  <comment>Icon Type:</comment>  ' . $iconType);
        $output->writeln('  <comment>Position:</comment>   ' . $position);
        $output->writeln('  <comment>Capability:</comment> ' . $capability);
        $output->writeln('  <comment>Vite Entry:</comment> ' . ($viteEntry !== '' ? $viteEntry : '(none)'));
        $output->writeln('  <comment>Asset Handle:</comment> ' . $assetHandle);
        $output->writeln('  <comment>Mount ID:</comment>    ' . $mountId);
        $output->writeln('  <comment>File:</comment>       ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your AppServiceProvider::boot(ApplicationInterface $app):');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot($app);</comment>');
    }
}

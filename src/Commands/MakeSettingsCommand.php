<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Settings Command.
 *
 * Creates a new WordPress settings page class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeSettingsCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Settings';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:settings')
            ->setDescription('Create a new WordPress settings page class')
            ->addArgument('name', InputArgument::REQUIRED, 'Settings class name')
            ->addOption('settings-slug', null, InputOption::VALUE_OPTIONAL, 'Settings page slug')
            ->addOption('settings-title', null, InputOption::VALUE_OPTIONAL, 'Page title')
            ->addOption('menu-title', null, InputOption::VALUE_OPTIONAL, 'Menu title')
            ->addOption('parent', null, InputOption::VALUE_OPTIONAL, 'Parent menu slug', 'options-general.php')
            ->addOption('capability', null, InputOption::VALUE_OPTIONAL, 'Required capability', 'manage_options');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'settings';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Admin\\Settings';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Admin/Settings';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [
            'settingsSlug' => $input->getOption('settings-slug') ?: $this->kebab($nameData['class']),
            'settingsTitle' => $input->getOption('settings-title') ?: $nameData['title'] . ' Settings',
            'menuTitle' => $input->getOption('menu-title') ?: $nameData['title'],
            'parentSlug' => $input->getOption('parent') ?: 'options-general.php',
            'capability' => $input->getOption('capability') ?: 'manage_options',
            'optionGroup' => $this->snake($nameData['class']) . '_options',
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
        $settingsSlug = $input->getOption('settings-slug') ?: $this->kebab($nameData['class']);
        $settingsTitle = $input->getOption('settings-title') ?: $nameData['title'] . ' Settings';
        $menuTitle = $input->getOption('menu-title') ?: $nameData['title'];
        $parent = $input->getOption('parent') ?: 'options-general.php';
        $capability = $input->getOption('capability') ?: 'manage_options';

        $output->writeln('');
        $output->writeln('<info>Settings Details:</info>');
        $output->writeln('  <comment>Class:</comment>      ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>  ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Slug:</comment>       ' . $settingsSlug);
        $output->writeln('  <comment>Title:</comment>      ' . $settingsTitle);
        $output->writeln('  <comment>Menu Title:</comment> ' . $menuTitle);
        $output->writeln('  <comment>Parent:</comment>     ' . ($parent ?: 'Top-level menu'));
        $output->writeln('  <comment>Capability:</comment> ' . $capability);
        $output->writeln('  <comment>File:</comment>       ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');
    }
}

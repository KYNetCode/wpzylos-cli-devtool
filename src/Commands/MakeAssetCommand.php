<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Asset Command.
 *
 * Creates a new asset management class for CSS/JS registration and enqueuing.
 * Assets provide a structured way to manage plugin scripts and stylesheets
 * with proper WordPress hooks integration.
 *
 * Usage:
 *   wpzylos make:asset AdminAssets
 *   wpzylos make:asset FrontendStyles --admin
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeAssetCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Asset';

    /** @inheritDoc */
    protected array $requiredPackages = ['wpzylos-assets'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:asset')
            ->setDescription('Create a new asset management class')
            ->setHelp(
                'This command creates an asset class for managing CSS/JS files.' . PHP_EOL .
                'The generated class handles registration, enqueuing, and versioning.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:asset AdminAssets</comment>' . PHP_EOL .
                '  <comment>wpzylos make:asset FrontendStyles --admin</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Asset class name (e.g., AdminAssets)')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Generate admin-specific asset class');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'asset';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Assets';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Assets';
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $isAdmin = $input->getOption('admin');
        $hook = $isAdmin ? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';

        $output->writeln('');
        $output->writeln('<info>Asset Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Hook:</comment>      ' . $hook);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Registration:</info>');
        $output->writeln('  Add to your AssetsServiceProvider::boot() method:');
        $output->writeln('  <comment>$asset = new \\' . $qualifiedName . '();</comment>');

        if ($isAdmin) {
            $output->writeln('  <comment>add_action(\'admin_enqueue_scripts\', [$asset, \'register\']);</comment>');
            $output->writeln('  <comment>add_action(\'admin_enqueue_scripts\', [$asset, \'enqueueAdmin\']);</comment>');
        } else {
            $output->writeln('  <comment>add_action(\'wp_enqueue_scripts\', [$asset, \'register\']);</comment>');
            $output->writeln('  <comment>add_action(\'wp_enqueue_scripts\', [$asset, \'enqueue\']);</comment>');
        }

        $output->writeln('');
        $output->writeln('<info>Asset files:</info>');
        $output->writeln('  <comment>resources/assets/css/' . $nameData['slug'] . '.css</comment>');
        $output->writeln('  <comment>resources/assets/js/' . $nameData['slug'] . '.js</comment>');
    }
}

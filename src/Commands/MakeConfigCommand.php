<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WPZylos\Framework\Cli\Core\FileWriter;
use WPZylos\Framework\Cli\Core\StubCompiler;
use WPZylos\Framework\Cli\DevTool\Support\ContextResolver;

/**
 * Make Config Command.
 *
 * Creates a new configuration file in the plugin's config/ directory.
 * Config files return a PHP array and do not use namespaces/classes.
 *
 * Usage:
 *   wpzylos make:config cache
 *   wpzylos make:config payments
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeConfigCommand extends Command
{
    /**
     * @var ContextResolver
     */
    protected ContextResolver $contextResolver;

    /**
     * @var FileWriter
     */
    protected FileWriter $fileWriter;

    /**
     * @var array<string, mixed>|null
     */
    protected ?array $context = null;

    public function __construct()
    {
        parent::__construct();
        $this->contextResolver = new ContextResolver();
        $this->fileWriter = new FileWriter();
    }

    protected function configure(): void
    {
        $this
            ->setName('make:config')
            ->setDescription('Create a new configuration file')
            ->setHelp(
                'This command creates a new configuration file in config/ directory.' . PHP_EOL .
                'Config files return a PHP array — no class or namespace needed.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:config cache</comment>' . PHP_EOL .
                '  <comment>wpzylos make:config payments</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Config file name (e.g., cache)')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Plugin root path', null)
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview without creating');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Resolve context
        $path = $input->getOption('path') ?? getcwd();
        $this->context = $this->contextResolver->tryResolve($path);

        if ($this->context === null) {
            $this->context = [
                'namespace' => 'MyPlugin',
                'slug' => 'my-plugin',
                'prefix' => 'mp_',
                'textDomain' => 'my-plugin',
            ];
        }

        $name = trim((string) $input->getArgument('name'));
        $name = str_replace(['\\', '/'], '-', $name);
        $name = strtolower($name);

        // Build file
        $pluginPath = $this->contextResolver->getPluginRoot($path) ?? $path;
        $filePath = rtrim($pluginPath, '/\\') . '/config/' . $name . '.php';

        // Check existence
        if (!$input->getOption('force') && file_exists($filePath)) {
            $output->writeln('<error>Config file already exists!</error>');
            $output->writeln('Use --force to overwrite.');
            return Command::FAILURE;
        }

        // Compile stub
        $stubPath = dirname(__DIR__, 2) . '/stubs';
        $compiler = new StubCompiler($stubPath);

        // Convert name to title for header
        $title = ucwords(str_replace(['-', '_'], ' ', $name));

        $content = $compiler->compile('config', [
            'title' => $title,
            'rootNamespace' => $this->context['namespace'] ?? 'MyPlugin',
        ]);

        // Dry run
        if ($input->getOption('dry-run')) {
            $relativePath = str_replace($pluginPath . DIRECTORY_SEPARATOR, '', $filePath);
            $output->writeln('<info>[DRY RUN] Would create: ' . $relativePath . '</info>');
            $output->writeln('');
            $output->writeln('<comment>Generated code preview:</comment>');
            $output->writeln('');
            $output->writeln($content);
            return Command::SUCCESS;
        }

        // Write file
        try {
            $this->fileWriter->setOverwrite((bool) $input->getOption('force'));
            $this->fileWriter->write($filePath, $content);

            $relativePath = str_replace($pluginPath . DIRECTORY_SEPARATOR, '', $filePath);
            $output->writeln('<info>Config [' . $relativePath . '] created successfully.</info>');
        } catch (RuntimeException $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  Load in your service provider:');
        $output->writeln('  <comment>$config = require plugin_path(\'config/' . $name . '.php\');</comment>');

        return Command::SUCCESS;
    }
}

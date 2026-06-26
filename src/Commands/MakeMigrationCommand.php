<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WPZylos\Framework\Cli\Core\FileWriter;
use WPZylos\Framework\Cli\Core\StubCompiler;

/**
 * Make Migration Command.
 *
 * Creates a new database migration file for WPZylos plugins.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeMigrationCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Migration';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-database'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:migration')
            ->setDescription('Create a new migration class')
            ->addArgument('name', InputArgument::REQUIRED, 'Migration name (e.g., create_users_table)')
            ->addOption('create', null, InputOption::VALUE_OPTIONAL, 'The table to be created')
            ->addOption('table', null, InputOption::VALUE_OPTIONAL, 'The table to modify');
    }

    /**
     * Override execute to handle timestamp-based filenames.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Check required packages
        if (!$this->checkPackages($output)) {
            return Command::FAILURE;
        }

        // Resolve context
        if (!$this->resolveContext($input, $output)) {
            return Command::FAILURE;
        }

        // Validate name
        $name = $this->getNameArgument($input);

        if (empty($name)) {
            $output->writeln('<error>The name argument is required.</error>');
            return Command::FAILURE;
        }

        // Generate filename with timestamp
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.php";

        // Convert to class name
        $className = $this->toClassName($name);

        // Extract table name from migration name or options
        $tableName = $input->getOption('create')
            ?: $input->getOption('table')
            ?: $this->extractTableName($name);

        // Build path
        $path = $input->getOption('path') ?? getcwd();
        $pluginRoot = $this->contextResolver->getPluginRoot($path) ?? $path;
        $filePath = rtrim($pluginRoot, '/\\') . '/database/migrations/' . $filename;

        // Check if file exists
        if (!$input->getOption('force') && file_exists($filePath)) {
            $output->writeln("<error>{$this->type} already exists!</error>");
            $output->writeln("Use --force to overwrite.");
            return Command::FAILURE;
        }

        // Build the content
        $compiler = $this->getStubCompiler();
        $namespace = ($this->context['namespace'] ?? 'MyPlugin') . '\\Database\\Migrations';

        $content = $compiler->compile('migration', [
            'namespace' => $namespace,
            'class' => $className,
            'prefix' => $this->context['prefix'] ?? 'mp_',
            'table' => $tableName,
        ]);

        // Dry run mode
        if ($input->getOption('dry-run')) {
            $output->writeln("<info>[DRY RUN] Would create: database/migrations/{$filename}</info>");
            $output->writeln('');
            $output->writeln('<comment>Generated code preview:</comment>');
            $output->writeln('');
            $output->writeln($content);
            return Command::SUCCESS;
        }

        // Write the file
        try {
            $this->fileWriter->setOverwrite((bool) $input->getOption('force'));
            $this->fileWriter->write($filePath, $content);
            $output->writeln("<info>{$this->type} [database/migrations/{$filename}] created successfully.</info>");
        } catch (\RuntimeException $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        // Show details
        $output->writeln('');
        $output->writeln('<info>Migration Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $className);
        $output->writeln('  <comment>Namespace:</comment> ' . $namespace);
        $output->writeln('  <comment>Table:</comment>     ' . $tableName);
        $output->writeln('  <comment>File:</comment>      database/migrations/' . $filename);

        $output->writeln('');
        $output->writeln('<info>Next steps:</info>');
        $output->writeln('  Run migration: <comment>wpzylos migrate</comment>');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'migration';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Database\\Migrations';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'database/migrations';
    }

    /**
     * Convert migration name to class name.
     */
    private function toClassName(string $name): string
    {
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        return str_replace(' ', '', $name);
    }

    /**
     * Extract table name from migration name.
     */
    private function extractTableName(string $name): string
    {
        // create_users_table -> users
        // add_email_to_users -> users
        $name = preg_replace('/^create_/', '', $name);
        $name = preg_replace('/_table$/', '', $name);

        return $name ?? '';
    }
}

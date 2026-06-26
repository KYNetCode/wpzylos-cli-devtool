<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Model Command.
 *
 * Creates a new model class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeModelCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Model';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-model'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:model')
            ->setDescription('Create a new model class')
            ->addArgument('name', InputArgument::REQUIRED, 'Model class name')
            ->addOption('table', 't', InputOption::VALUE_OPTIONAL, 'Database table name')
            ->addOption('migration', 'm', InputOption::VALUE_NONE, 'Create a new migration for the model')
            ->addOption('controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model')
            ->addOption('resource', 'r', InputOption::VALUE_NONE, 'Create a resource controller for the model');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'model';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Models';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Models';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $table = $input->getOption('table');

        if (empty($table)) {
            // Convert class name to table name (plural snake_case)
            $table = $this->snake($this->plural($nameData['class']));
        }

        return [
            'table' => $table,
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
        $table = $input->getOption('table') ?: $this->snake($this->plural($nameData['class']));

        $output->writeln('');
        $output->writeln('<info>Model Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Table:</comment>     ' . $table);
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        // Create migration if requested
        if ($input->getOption('migration')) {
            $output->writeln('');
            $this->runMakeMigration($nameData['class'], $table, $output);
        }

        // Create controller if requested
        if ($input->getOption('controller') || $input->getOption('resource')) {
            $output->writeln('');
            $this->runMakeController($nameData['class'], $input->getOption('resource'), $output);
        }
    }

    /**
     * Run make:migration command.
     */
    private function runMakeMigration(string $modelName, string $table, OutputInterface $output): void
    {
        $output->writeln('<info>Creating migration...</info>');
        $output->writeln("  Run: <comment>wpzylos make:migration create_{$table}_table --create={$table}</comment>");
    }

    /**
     * Run make:controller command.
     */
    private function runMakeController(string $modelName, bool $resource, OutputInterface $output): void
    {
        $controllerType = $resource ? 'resource controller' : 'controller';
        $output->writeln("<info>Creating {$controllerType}...</info>");
        $flag = $resource ? ' --resource' : '';
        $output->writeln("  Run: <comment>wpzylos make:controller {$modelName}Controller{$flag}</comment>");
    }
}

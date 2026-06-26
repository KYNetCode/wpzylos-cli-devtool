<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Controller Command.
 *
 * Creates a new controller class for WPZylos plugins.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeControllerCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Controller';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-http'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:controller')
            ->setDescription('Create a new controller class')
            ->addArgument('name', InputArgument::REQUIRED, 'Controller class name')
            ->addOption('resource', 'r', InputOption::VALUE_NONE, 'Create a resource controller')
            ->addOption('api', null, InputOption::VALUE_NONE, 'Create an API controller')
            ->addOption('model', 'm', InputOption::VALUE_OPTIONAL, 'Model to use for resource');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        // Use wpzylos-cli-core stubs
        return 'controller';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Http\\Controllers';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Http/Controllers';
    }

    /**
     * @inheritDoc
     */
    protected function parseName(string $name): array
    {
        $data = parent::parseName($name);

        // Remove the 'Controller' suffix if provided
        $data['class'] = preg_replace('/Controller$/', '', $data['class']);

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function getFilePath(array $nameData): string
    {
        $path = $this->getPluginPath();
        $directory = rtrim($path, '/\\') . '/' . $this->getDefaultOutputDirectory();

        if (!empty($nameData['path'])) {
            $directory .= '/' . str_replace('\\', '/', $nameData['path']);
        }

        // Add 'Controller' suffix to filename
        return $directory . '/' . $nameData['class'] . 'Controller.php';
    }

    /**
     * @inheritDoc
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [
            'view' => strtolower($nameData['class']),
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
        $output->writeln('');
        $output->writeln('<info>Controller Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class'] . 'Controller');
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Route registration:</info>');
        $output->writeln('  <comment>$router->get(\'/\', [\\' . $qualifiedName . '::class, \'index\']);</comment>');
    }
}

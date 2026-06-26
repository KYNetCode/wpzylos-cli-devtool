<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Request Command.
 *
 * Creates a new form request class for WPZylos plugins.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeRequestCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Request';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-validation'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:request')
            ->setDescription('Create a new form request class')
            ->addArgument('name', InputArgument::REQUIRED, 'Request class name');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'request';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Http\\Requests';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Http/Requests';
    }

    /**
     * @inheritDoc
     */
    protected function parseName(string $name): array
    {
        $data = parent::parseName($name);

        // Remove the 'Request' suffix if provided
        $data['class'] = preg_replace('/Request$/', '', $data['class']);

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

        // Add 'Request' suffix to filename
        return $directory . '/' . $nameData['class'] . 'Request.php';
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
        $output->writeln('<info>Request Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class'] . 'Request');
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  <comment>$request = new \\' . $qualifiedName . '($_POST);</comment>');
        $output->writeln('  <comment>if ($request->validate()) { ... }</comment>');
    }
}

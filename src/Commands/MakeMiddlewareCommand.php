<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Middleware Command.
 *
 * Creates a new HTTP middleware class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeMiddlewareCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Middleware';

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
            ->setName('make:middleware')
            ->setDescription('Create a new HTTP middleware class')
            ->addArgument('name', InputArgument::REQUIRED, 'Middleware class name');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'middleware';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Http\\Middleware';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Http/Middleware';
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
        $output->writeln('<info>Middleware Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Next steps:</info>');
        $output->writeln('  Register this middleware in your HTTP kernel or route group:');
        $output->writeln('  <comment>$router->middleware([\\' . $qualifiedName . '::class]);</comment>');
    }
}

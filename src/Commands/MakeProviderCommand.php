<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Provider Command.
 *
 * Creates a new service provider class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeProviderCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Provider';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-core'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:provider')
            ->setDescription('Create a new service provider class')
            ->addArgument('name', InputArgument::REQUIRED, 'Provider class name')
            ->addOption('deferred', null, InputOption::VALUE_NONE, 'Create a deferred provider');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'provider';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Providers';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Providers';
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
        $output->writeln('<info>Provider Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Next steps:</info>');
        $output->writeln('  Register this provider in your plugin bootstrap:');
        $output->writeln('  <comment>$container->addProvider(new \\' . $qualifiedName . '());</comment>');
    }
}

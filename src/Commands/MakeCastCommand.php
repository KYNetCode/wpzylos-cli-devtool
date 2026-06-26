<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Cast Command.
 *
 * Creates a custom attribute cast class with get/set accessors
 * for transforming model attributes between application and database.
 *
 * Usage:
 *   wpzylos make:cast JsonCast
 *   wpzylos make:cast Casts/MoneyCast
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeCastCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Cast';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:cast')
            ->setDescription('Create a new custom attribute cast')
            ->setHelp(
                'This command creates a custom attribute cast class.' . PHP_EOL .
                'Casts transform values between application and database formats.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:cast JsonCast</comment>' . PHP_EOL .
                '  <comment>wpzylos make:cast Casts/MoneyCast</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Cast class name (e.g., JsonCast)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'cast';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Casts';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Casts';
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln('');
        $output->writeln('<info>Cast Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage in Model:</info>');
        $output->writeln('  <comment>protected array $casts = [');
        $output->writeln('      \'field\' => \\' . $qualifiedName . '::class,');
        $output->writeln('  ];</comment>');
    }
}

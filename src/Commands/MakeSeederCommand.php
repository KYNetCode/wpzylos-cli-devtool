<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Seeder Command.
 *
 * Creates a new database seeder class.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeSeederCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Seeder';

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
            ->setName('make:seeder')
            ->setDescription('Create a new database seeder class')
            ->addArgument('name', InputArgument::REQUIRED, 'Seeder class name');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'seeder';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Database\\Seeders';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'database/seeders';
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
        $output->writeln('<info>Seeder Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Next steps:</info>');
        $output->writeln('  1. Implement the run() method with your seeding logic');
        $output->writeln('  2. Run the seeder with: <comment>wpzylos db:seed --class=' . $nameData['class'] . '</comment>');
    }
}

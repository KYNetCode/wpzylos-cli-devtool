<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Service Command.
 *
 * Creates a new service class for business logic encapsulation.
 * Services provide a dedicated layer for complex operations,
 * keeping controllers thin and models focused on data access.
 *
 * Usage:
 *   wpzylos make:service PaymentProcessor
 *   wpzylos make:service Billing/InvoiceService
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeServiceCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Service';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:service')
            ->setDescription('Create a new service class')
            ->setHelp(
                'This command creates a new service class for encapsulating business logic.' . PHP_EOL .
                PHP_EOL .
                'Services act as a dedicated layer between controllers and models,' . PHP_EOL .
                'keeping your application organized and testable.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:service PaymentProcessor</comment>' . PHP_EOL .
                '  <comment>wpzylos make:service Billing/InvoiceService</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Service class name (e.g., PaymentProcessor)');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'service';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Services';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Services';
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
        $output->writeln('<info>Service Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  Inject or instantiate in your controller:');
        $output->writeln('  <comment>$service = new \\' . $qualifiedName . '();</comment>');
        $output->writeln('  <comment>$result = $service->execute($data);</comment>');
    }
}

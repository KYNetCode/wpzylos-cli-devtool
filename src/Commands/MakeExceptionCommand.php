<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Exception Command.
 *
 * Creates a new custom exception class extending RuntimeException.
 *
 * Usage:
 *   wpzylos make:exception PaymentFailedException
 *   wpzylos make:exception Api/RateLimitException --render
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeExceptionCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Exception';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:exception')
            ->setDescription('Create a new custom exception class')
            ->setHelp(
                'This command creates a custom exception class extending RuntimeException.' . PHP_EOL .
                'The exception includes a report() method for custom error handling.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:exception PaymentFailedException</comment>' . PHP_EOL .
                '  <comment>wpzylos make:exception Api/RateLimitException</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Exception class name (e.g., PaymentFailedException)')
            ->addOption('render', null, InputOption::VALUE_NONE, 'Add a render() method for HTTP response rendering');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'exception';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Exceptions';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Exceptions';
    }

    /**
     * @inheritDoc
     */
    protected function buildClass(string $qualifiedName, array $nameData, InputInterface $input): string
    {
        $content = parent::buildClass($qualifiedName, $nameData, $input);

        // Add render() method if --render flag is used
        if ($input->getOption('render')) {
            $renderMethod = <<<'PHP'

    /**
     * Render the exception as an HTTP response.
     *
     * @return array<string, mixed>
     */
    public function render(): array
    {
        return [
            'error'   => true,
            'message' => $this->getMessage(),
            'code'    => $this->getCode(),
        ];
    }
PHP;
            // Insert before the last closing brace
            $content = preg_replace('/}\s*$/', $renderMethod . "\n}\n", $content);
        }

        return $content;
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
        $output->writeln('<info>Exception Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        if ($input->getOption('render')) {
            $output->writeln('  <comment>Render:</comment>    Yes (HTTP response rendering)');
        }

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  <comment>throw new \\' . $qualifiedName . '(\'Something went wrong.\');</comment>');
    }
}

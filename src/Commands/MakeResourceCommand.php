<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Resource Command.
 *
 * Creates an API resource transformer for consistent JSON serialization
 * of model data, designed for WordPress REST API responses.
 *
 * Usage:
 *   wpzylos make:resource UserResource
 *   wpzylos make:resource Api/OrderResource
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeResourceCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Resource';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:resource')
            ->setDescription('Create a new API resource transformer')
            ->setHelp(
                'This command creates an API resource transformer class.' . PHP_EOL .
                'Resources transform model data into consistent JSON for' . PHP_EOL .
                'WordPress REST API responses.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:resource UserResource</comment>' . PHP_EOL .
                '  <comment>wpzylos make:resource Api/OrderResource</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Resource class name (e.g., UserResource)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'resource';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Http\\Resources';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Http/Resources';
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
        $output->writeln('<info>Resource Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage in REST controller:</info>');
        $output->writeln('  <comment>$resource = new \\' . $qualifiedName . '($model);</comment>');
        $output->writeln('  <comment>return new \\WP_REST_Response($resource->toArray(), 200);</comment>');

        $output->writeln('');
        $output->writeln('<info>Collection usage:</info>');
        $output->writeln('  <comment>$items = \\' . $qualifiedName . '::collection($models);</comment>');
    }
}

<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make REST Command.
 *
 * Creates a new WordPress REST API controller class with full CRUD endpoints.
 *
 * Usage:
 *   wpzylos make:rest ProductController
 *   wpzylos make:rest OrderController --route=orders
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeRestCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'REST Controller';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:rest')
            ->setDescription('Create a new WordPress REST API controller')
            ->setHelp(
                'This command creates a REST API controller with CRUD endpoints,' . PHP_EOL .
                'permission callbacks, and argument validation.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:rest ProductController</comment>' . PHP_EOL .
                '  <comment>wpzylos make:rest OrderController --route=orders</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'REST controller class name')
            ->addOption('route', null, InputOption::VALUE_OPTIONAL, 'REST route path (defaults to kebab-case of class name)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'rest';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Http\\Rest';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Http/Rest';
    }

    /** @inheritDoc */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $route = $input->getOption('route') ?: $this->kebab($nameData['class']);
        // Strip common suffixes from route
        $route = preg_replace('/-(controller|rest|api)$/i', '', $route) ?: $route;

        return [
            'restRoute' => $route,
        ];
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $slug = $this->context['slug'] ?? 'my-plugin';
        $route = $input->getOption('route') ?: $this->kebab($nameData['class']);
        $route = preg_replace('/-(controller|rest|api)$/i', '', $route) ?: $route;

        $output->writeln('');
        $output->writeln('<info>REST Controller Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Endpoints:</info>');
        $output->writeln('  <comment>GET    /wp-json/' . $slug . '/v1/' . $route . '</comment>');
        $output->writeln('  <comment>POST   /wp-json/' . $slug . '/v1/' . $route . '</comment>');
        $output->writeln('  <comment>GET    /wp-json/' . $slug . '/v1/' . $route . '/{id}</comment>');
        $output->writeln('  <comment>PUT    /wp-json/' . $slug . '/v1/' . $route . '/{id}</comment>');
        $output->writeln('  <comment>DELETE /wp-json/' . $slug . '/v1/' . $route . '/{id}</comment>');

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');
    }
}

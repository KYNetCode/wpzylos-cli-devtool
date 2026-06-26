<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Shortcode Command.
 *
 * Creates a WPZylos-aware shortcode with on-demand Vite assets and graceful
 * Gutenberg, Elementor, and WPBakery integrations.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeShortcodeCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Shortcode';

    /** Configure the command. */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:shortcode')
            ->setDescription('Create a builder-compatible WPZylos shortcode class')
            ->setHelp(
                'Creates a shortcode using ApplicationInterface, HookManager, ' .
                'ContextInterface, and ViteAssetResolver.' . PHP_EOL . PHP_EOL .
                'The class also provides a dynamic Gutenberg block and graceful ' .
                'Elementor/WPBakery bridges.' . PHP_EOL . PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:shortcode PricingTable</comment>' . PHP_EOL .
                '  <comment>wpzylos make:shortcode PricingTable --tag=pricing_table</comment>' . PHP_EOL .
                '  <comment>wpzylos make:shortcode PricingTable ' .
                '--title="Pricing Table" --entry=resources/js/pricing.js</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Shortcode class name (e.g., PricingTable)')
            ->addOption('tag', 't', InputOption::VALUE_OPTIONAL, 'Shortcode tag (defaults to snake_case class name)')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Human-readable builder title')
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, 'Builder description', 'A custom shortcode.')
            ->addOption('category', null, InputOption::VALUE_OPTIONAL, 'Gutenberg/WPBakery category', 'widgets')
            ->addOption('icon', null, InputOption::VALUE_OPTIONAL, 'Gutenberg dashicon name', 'shortcode')
            ->addOption(
                'entry',
                null,
                InputOption::VALUE_OPTIONAL,
                'Vite entry enqueued only when rendered',
                'resources/js/app.js'
            );
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'shortcode';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Shortcodes';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Shortcodes';
    }

    /** @inheritDoc */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        $tag = (string) ($input->getOption('tag') ?: $this->snake($nameData['class']));
        $title = (string) ($input->getOption('title') ?: $nameData['title']);
        $description = (string) ($input->getOption('description') ?: 'A custom shortcode.');
        $category = (string) ($input->getOption('category') ?: 'widgets');
        $icon = (string) ($input->getOption('icon') ?: 'shortcode');
        $entry = (string) ($input->getOption('entry') ?: 'resources/js/app.js');

        if (!preg_match('/^[a-z0-9_-]+$/', $tag)) {
            throw new \InvalidArgumentException(
                'The shortcode tag may only contain lowercase letters, numbers, underscores, and hyphens.'
            );
        }

        return [
            'tag' => $tag,
            'blockName' => ($this->context['slug'] ?? 'my-plugin') . '/' . str_replace('_', '-', $tag),
            'shortcodeTitle' => $this->escapePhpString($title),
            'shortcodeDescription' => $this->escapePhpString($description),
            'shortcodeCategory' => $this->escapePhpString($category),
            'shortcodeIcon' => $this->escapePhpString($icon),
            'viteEntry' => $this->escapePhpString($entry),
        ];
    }

    /** Escape a value for a generated single-quoted PHP string. */
    private function escapePhpString(string $value): string
    {
        return str_replace(['\\', "'"], ['\\\\', "\\'"], $value);
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $tag = $input->getOption('tag') ?: $this->snake($nameData['class']);

        $output->writeln('');
        $output->writeln('<info>Shortcode Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Tag:</comment>       [' . $tag . ']');
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Service provider boot:</info>');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot($app);</comment>');

        $output->writeln('');
        $output->writeln('<info>Builder support:</info>');
        $output->writeln('  Gutenberg dynamic block, Elementor widget, and WPBakery element');

        $output->writeln('');
        $output->writeln('<info>Usage in content:</info>');
        $output->writeln('  <comment>[' . $tag . ']</comment>');
        $output->writeln('  <comment>[' . $tag . ']Content here[/' . $tag . ']</comment>');
    }
}

<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Block Command.
 *
 * Creates a new WordPress Gutenberg block with a folder-based structure:
 *   - PHP registration class
 *   - block.json metadata file
 *   - render.php server-side template
 *
 * Usage:
 *   wpzylos make:block HeroSection
 *   wpzylos make:block HeroSection --title="Hero Section" --category=design
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeBlockCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Block';

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:block')
            ->setDescription('Create a new WordPress Gutenberg block')
            ->setHelp(
                'This command scaffolds a complete Gutenberg block with:' . PHP_EOL .
                '  - A PHP registration class in app/WordPress/Blocks/' . PHP_EOL .
                '  - A block.json metadata file in blocks/<slug>/' . PHP_EOL .
                '  - A render.php template in blocks/<slug>/' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:block HeroSection</comment>' . PHP_EOL .
                '  <comment>wpzylos make:block HeroSection --title="Hero Section" --category=design</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Block class name (e.g., HeroSection)')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Block title for the editor')
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, 'Block description', 'A custom block.')
            ->addOption('icon', null, InputOption::VALUE_OPTIONAL, 'Block icon (dashicon name)', 'block-default')
            ->addOption('category', null, InputOption::VALUE_OPTIONAL, 'Block category', 'widgets');
    }

    /**
     * Override execute to handle multi-file block scaffolding.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Check required packages
        if (!$this->checkPackages($output)) {
            return Command::FAILURE;
        }

        // Resolve context
        if (!$this->resolveContext($input, $output)) {
            return Command::FAILURE;
        }

        // Validate name
        $name = $this->getNameArgument($input);
        if (!$this->validateName($name, $output)) {
            return Command::FAILURE;
        }

        // Parse name into components
        $nameData = $this->parseName($name);

        // Build paths
        $pluginRoot = $this->getPluginPath();
        $blockSlug = $this->kebab($nameData['class']);
        $classFilePath = rtrim($pluginRoot, '/\\') . '/app/WordPress/Blocks/' . $nameData['class'] . '.php';
        $blockDir = rtrim($pluginRoot, '/\\') . '/blocks/' . $blockSlug;
        $blockJsonPath = $blockDir . '/block.json';
        $renderPath = $blockDir . '/render.php';

        // Check if files exist
        $force = (bool) $input->getOption('force');
        if (!$force && (file_exists($classFilePath) || file_exists($blockJsonPath))) {
            $output->writeln("<error>{$this->type} already exists!</error>");
            $output->writeln("Use --force to overwrite.");
            return Command::FAILURE;
        }

        // Prepare shared replacements
        $blockNamespace = $this->context['slug'] ?? 'my-plugin';
        $blockTitle = $input->getOption('title') ?: $nameData['title'];
        $blockDescription = $input->getOption('description') ?: 'A custom block.';
        $blockIcon = $input->getOption('icon') ?: 'block-default';
        $blockCategory = $input->getOption('category') ?: 'widgets';

        $qualifiedName = $this->getQualifiedClassName($nameData);
        $namespace = $this->getNamespace($qualifiedName);

        $customTokens = [
            'namespace'        => $namespace,
            'class'            => $nameData['class'],
            'blockNamespace'   => $blockNamespace,
            'blockSlug'        => $blockSlug,
            'blockTitle'       => $blockTitle,
            'blockDescription' => $blockDescription,
            'blockIcon'        => $blockIcon,
            'blockCategory'    => $blockCategory,
            'textDomain'       => $this->context['textDomain'] ?? 'my-plugin',
            'rootNamespace'    => $this->context['namespace'] ?? 'MyPlugin',
            'prefix'           => $this->context['prefix'] ?? 'mp_',
            'slug'             => $nameData['slug'],
            'snake'            => $nameData['snake'],
            'title'            => $nameData['title'],
        ];

        // Dry run mode
        if ($input->getOption('dry-run')) {
            $output->writeln("<info>[DRY RUN] Would create the following files:</info>");
            $output->writeln("  - " . $this->getRelativePath($classFilePath));
            $output->writeln("  - blocks/{$blockSlug}/block.json");
            $output->writeln("  - blocks/{$blockSlug}/render.php");
            $output->writeln('');
            $output->writeln('<comment>Block class preview:</comment>');
            $output->writeln('');
            $compiler = $this->getStubCompiler();
            $output->writeln($compiler->compile('block', $customTokens));
            return Command::SUCCESS;
        }

        // Compile and write all files
        $compiler = $this->getStubCompiler();
        $this->fileWriter->setOverwrite($force);

        try {
            // 1. PHP class
            $classContent = $compiler->compile('block', $customTokens);
            $this->fileWriter->write($classFilePath, $classContent);

            // 2. block.json
            $jsonContent = $compiler->compile('block.json', $customTokens);
            $this->fileWriter->write($blockJsonPath, $jsonContent);

            // 3. render.php
            $renderContent = $compiler->compile('render', $customTokens);
            $this->fileWriter->write($renderPath, $renderContent);

            $output->writeln("<info>{$this->type} [{$nameData['class']}] created successfully.</info>");
        } catch (RuntimeException $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        // Post-generation details
        $output->writeln('');
        $output->writeln('<info>Block Details:</info>');
        $output->writeln('  <comment>Class:</comment>       ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment>   ' . $namespace);
        $output->writeln('  <comment>Block Type:</comment>  ' . $blockNamespace . '/' . $blockSlug);
        $output->writeln('  <comment>Title:</comment>       ' . $blockTitle);
        $output->writeln('  <comment>Category:</comment>    ' . $blockCategory);
        $output->writeln('  <comment>Icon:</comment>        ' . $blockIcon);

        $output->writeln('');
        $output->writeln('<info>Files created:</info>');
        $output->writeln('  <comment>' . $this->getRelativePath($classFilePath) . '</comment>');
        $output->writeln('  <comment>blocks/' . $blockSlug . '/block.json</comment>');
        $output->writeln('  <comment>blocks/' . $blockSlug . '/render.php</comment>');

        $output->writeln('');
        $output->writeln('<info>Bootstrap:</info>');
        $output->writeln('  Add to your plugin bootstrap:');
        $output->writeln('  <comment>\\' . $qualifiedName . '::boot();</comment>');

        $output->writeln('');
        $output->writeln('<info>Next steps:</info>');
        $output->writeln('  1. Add editor JavaScript in <comment>blocks/' . $blockSlug . '/index.js</comment>');
        $output->writeln('  2. Customize the render template in <comment>blocks/' . $blockSlug . '/render.php</comment>');
        $output->writeln('  3. Update block attributes in <comment>blocks/' . $blockSlug . '/block.json</comment>');

        return Command::SUCCESS;
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'block';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'WordPress\\Blocks';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/WordPress/Blocks';
    }
}

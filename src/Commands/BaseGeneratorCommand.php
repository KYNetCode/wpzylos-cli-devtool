<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use RuntimeException;
use Throwable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WPZylos\Framework\Cli\Core\FileWriter;
use WPZylos\Framework\Cli\Core\StubCompiler;
use WPZylos\Framework\Cli\DevTool\Support\ContextResolver;
use WPZylos\Framework\Cli\DevTool\Support\PackageChecker;

/**
 * Base Generator Command.
 *
 * Abstract base class for all file generation commands.
 * Provides shared functionality for context resolution, stub compilation,
 * file writing, and consistent CLI output.
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
abstract class BaseGeneratorCommand extends Command
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected string $type = 'Class';

    /**
     * Required packages for this command.
     *
     * @var string[]
     */
    protected array $requiredPackages = [];

    /**
     * Reserved names that cannot be used.
     *
     * @var string[]
     */
    protected array $reservedNames = [
        '__halt_compiler',
        'abstract',
        'and',
        'array',
        'as',
        'break',
        'callable',
        'case',
        'catch',
        'class',
        'clone',
        'const',
        'continue',
        'declare',
        'default',
        'die',
        'do',
        'echo',
        'else',
        'elseif',
        'empty',
        'enddeclare',
        'endfor',
        'endforeach',
        'endif',
        'endswitch',
        'endwhile',
        'eval',
        'exit',
        'extends',
        'final',
        'finally',
        'fn',
        'for',
        'foreach',
        'function',
        'global',
        'goto',
        'if',
        'implements',
        'include',
        'include_once',
        'instanceof',
        'insteadof',
        'interface',
        'isset',
        'list',
        'match',
        'namespace',
        'new',
        'or',
        'print',
        'private',
        'protected',
        'public',
        'readonly',
        'require',
        'require_once',
        'return',
        'static',
        'switch',
        'throw',
        'trait',
        'try',
        'unset',
        'use',
        'var',
        'while',
        'xor',
        'yield',
    ];

    /**
     * @var ContextResolver
     */
    protected ContextResolver $contextResolver;

    /**
     * @var PackageChecker
     */
    protected PackageChecker $packageChecker;

    /**
     * @var StubCompiler|null
     */
    protected ?StubCompiler $stubCompiler = null;

    /**
     * @var FileWriter
     */
    protected FileWriter $fileWriter;

    /**
     * @var array<string, mixed>|null Resolved context
     */
    protected ?array $context = null;

    /** Resolved plugin root, including when --path is used outside the project. */
    protected ?string $pluginPath = null;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->contextResolver = new ContextResolver();
        $this->packageChecker = new PackageChecker();
        $this->fileWriter = new FileWriter();
    }

    /**
     * Configure common options for all generator commands.
     */
    protected function configure(): void
    {
        $this
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Plugin root path', null)
            ->addOption(
                'namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Root namespace (auto-detected from .plugin-config.json)'
            )
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing file')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview generated code without creating file');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
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

        // Get qualified class name and path
        $qualifiedName = $this->getQualifiedClassName($nameData);
        $filePath = $this->getFilePath($nameData);

        // Check if file exists
        if (!$input->getOption('force') && file_exists($filePath)) {
            $output->writeln("<error>{$this->type} already exists!</error>");
            $output->writeln("Use --force to overwrite.");
            return Command::FAILURE;
        }

        // Build the class content. Custom generators may reject invalid options.
        try {
            $content = $this->buildClass($qualifiedName, $nameData, $input);
        } catch (Throwable $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        // Dry run mode
        if ($input->getOption('dry-run')) {
            $output->writeln("<info>[DRY RUN] Would create: {$this->getRelativePath($filePath)}</info>");
            $output->writeln('');
            $output->writeln('<comment>Generated code preview:</comment>');
            $output->writeln('');
            $output->writeln($content);
            return Command::SUCCESS;
        }

        // Write the file
        try {
            $this->fileWriter->setOverwrite((bool) $input->getOption('force'));
            $this->fileWriter->write($filePath, $content);
            $output->writeln("<info>{$this->type} [{$this->getRelativePath($filePath)}] created successfully.</info>");
        } catch (RuntimeException $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        // Post-generation tasks
        $this->afterGeneration($qualifiedName, $nameData, $filePath, $input, $output);

        return Command::SUCCESS;
    }

    /**
     * Get the stub file name.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    abstract protected function getStub(InputInterface $input): string;

    /**
     * Get the default namespace segment.
     *
     * @return string
     */
    abstract protected function getDefaultNamespaceSegment(): string;

    /**
     * Get the default output directory relative to plugin root.
     *
     * @return string
     */
    abstract protected function getDefaultOutputDirectory(): string;

    /**
     * Get custom replacements for the stub.
     *
     * @param array<string, mixed> $nameData
     * @param InputInterface $input
     *
     * @return array<string, string>
     */
    protected function getCustomReplacements(array $nameData, InputInterface $input): array
    {
        return [];
    }

    /**
     * Tasks to run after file generation.
     *
     * @param string $qualifiedName
     * @param array<string, mixed> $nameData
     * @param string $filePath
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        // Override in child classes for post-generation tasks
    }

    /**
     * Check if required packages are available.
     *
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function checkPackages(OutputInterface $output): bool
    {
        if (count($this->requiredPackages) === 0) {
            return true;
        }

        $missing = $this->packageChecker->getMissing($this->requiredPackages);

        if (count($missing) > 0) {
            $output->writeln('<error>Missing required packages!</error>');
            $output->writeln('');
            $output->writeln($this->packageChecker->getMissingPackageMessage($missing));
            return false;
        }

        return true;
    }

    /**
     * Resolve plugin context from config or options.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function resolveContext(InputInterface $input, OutputInterface $output): bool
    {
        $path = $input->getOption('path') ?? getcwd();

        $this->pluginPath = $this->contextResolver->getPluginRoot((string) $path);

        // Try to resolve from config file
        $this->context = $this->contextResolver->tryResolve($path);

        // Override with CLI options if provided
        if ($input->getOption('namespace')) {
            $this->context = $this->context ?? [
                'namespace' => 'MyPlugin',
                'slug' => 'my-plugin',
                'prefix' => 'mp_',
                'cssPrefix' => 'mp-',
                'textDomain' => 'my-plugin',
                'version' => '1.0.0',
            ];
            $this->context['namespace'] = $input->getOption('namespace');
        }

        if ($this->context === null) {
            $output->writeln('<comment>Warning: Could not find .plugin-config.json</comment>');
            $output->writeln(
                '<comment>Using default values. Run "scaffold init" or provide --namespace option.</comment>'
            );
            $output->writeln('');

            $this->context = [
                'namespace' => 'MyPlugin',
                'slug' => 'my-plugin',
                'prefix' => 'mp_',
                'textDomain' => 'my-plugin',
                'version' => '1.0.0',
            ];
        }

        return true;
    }

    /**
     * Get the name argument.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getNameArgument(InputInterface $input): string
    {
        return trim((string) $input->getArgument('name'));
    }

    /**
     * Validate the given name.
     *
     * @param string $name
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function validateName(string $name, OutputInterface $output): bool
    {
        if (empty($name)) {
            $output->writeln('<error>The name argument is required.</error>');
            return false;
        }

        // Check for reserved names
        $baseName = basename(str_replace('\\', '/', $name));
        if (in_array(strtolower($baseName), $this->reservedNames, true)) {
            $output->writeln("<error>The name \"{$baseName}\" is reserved by PHP.</error>");
            return false;
        }

        return true;
    }

    /**
     * Parse the name into components.
     *
     * @param string $name
     *
     * @return array<string, mixed>
     */
    protected function parseName(string $name): array
    {
        $name = str_replace('/', '\\', $name);
        $parts = explode('\\', $name);
        $className = array_pop($parts);
        $subPath = implode('\\', $parts);

        // Convert to StudlyCase
        $studlyClass = $this->studly($className);

        return [
            'original' => $name,
            'class' => $studlyClass,
            'path' => $subPath,
            'parts' => $parts,
            'slug' => $this->kebab($className),
            'snake' => $this->snake($className),
            'camel' => $this->camel($className),
            'studly' => $studlyClass,
            'title' => $this->headline($className),
            'singular' => $studlyClass,
            'plural' => $this->plural($studlyClass),
        ];
    }

    /**
     * Get the fully qualified class name.
     *
     * @param array<string, mixed> $nameData
     *
     * @return string
     */
    protected function getQualifiedClassName(array $nameData): string
    {
        $rootNamespace = $this->context['namespace'] ?? 'MyPlugin';
        $namespace = rtrim($rootNamespace, '\\') . '\\' . $this->getDefaultNamespaceSegment();

        if (!empty($nameData['path'])) {
            $namespace .= '\\' . $nameData['path'];
        }

        return $namespace . '\\' . $nameData['class'];
    }

    /**
     * Get the file path for the generated class.
     *
     * @param array<string, mixed> $nameData
     *
     * @return string
     */
    protected function getFilePath(array $nameData): string
    {
        $path = $this->getPluginPath();
        $directory = rtrim($path, '/\\') . '/' . $this->getDefaultOutputDirectory();

        if (!empty($nameData['path'])) {
            $directory .= '/' . str_replace('\\', '/', $nameData['path']);
        }

        return $directory . '/' . $nameData['class'] . '.php';
    }

    /**
     * Get the plugin root path.
     *
     * @return string
     */
    protected function getPluginPath(): string
    {
        if ($this->pluginPath !== null) {
            return $this->pluginPath;
        }

        $cwd = getcwd() ?: '.';
        return $this->contextResolver->getPluginRoot($cwd) ?? $cwd;
    }

    /**
     * Get relative path for display.
     *
     * @param string $path
     *
     * @return string
     */
    protected function getRelativePath(string $path): string
    {
        $basePath = rtrim(str_replace('\\', '/', $this->getPluginPath()), '/');
        $path = str_replace('\\', '/', $path);

        if (str_starts_with($path, $basePath . '/')) {
            return substr($path, strlen($basePath) + 1);
        }

        return $path;
    }

    /**
     * Build the class content.
     *
     * @param string $qualifiedName
     * @param array<string, mixed> $nameData
     * @param InputInterface $input
     *
     * @return string
     */
    protected function buildClass(string $qualifiedName, array $nameData, InputInterface $input): string
    {
        $compiler = $this->getStubCompiler();

        $namespace = $this->getNamespace($qualifiedName);

        $replacements = array_merge([
            'namespace' => $namespace,
            'class' => $nameData['class'],
            'slug' => $nameData['slug'],
            'snake' => $nameData['snake'],
            'camel' => $nameData['camel'],
            'studly' => $nameData['studly'],
            'title' => $nameData['title'],
            'textDomain' => $this->context['textDomain'] ?? 'my-plugin',
            'prefix' => $this->context['prefix'] ?? 'mp_',
            'cssPrefix' => $this->context['cssPrefix'] ?? 'mp-',
            'rootNamespace' => $this->context['namespace'] ?? 'MyPlugin',
        ], $this->getCustomReplacements($nameData, $input));

        return $compiler->compile($this->getStub($input), $replacements);
    }

    /**
     * Get the namespace from a qualified class name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getNamespace(string $name): string
    {
        $pos = strrpos($name, '\\');
        return $pos === false ? '' : substr($name, 0, $pos);
    }

    /**
     * Get the stub compiler.
     *
     * @return StubCompiler
     */
    protected function getStubCompiler(): StubCompiler
    {
        if ($this->stubCompiler === null) {
            $this->stubCompiler = new StubCompiler($this->getStubPath());
        }

        return $this->stubCompiler;
    }

    /**
     * Get the stub directory path.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        // First check for stubs in this package
        $localPath = dirname(__DIR__, 2) . '/stubs';
        if (is_dir($localPath)) {
            return $localPath;
        }

        // Fall back to wpzylos-cli-core stubs
        return dirname(__DIR__, 3) . '/wpzylos-cli-core/stubs';
    }

    // =========================================================================
    // String Helpers (no Illuminate dependency)
    // =========================================================================

    /**
     * Convert string to StudlyCase.
     */
    protected function studly(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', $value);
        return str_replace(' ', '', ucwords($value));
    }

    /**
     * Convert string to camelCase.
     */
    protected function camel(string $value): string
    {
        return lcfirst($this->studly($value));
    }

    /**
     * Convert string to snake_case.
     */
    protected function snake(string $value): string
    {
        $value = preg_replace('/\s+/u', '', ucwords($value));
        return strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value) ?? $value);
    }

    /**
     * Convert string to kebab-case.
     */
    protected function kebab(string $value): string
    {
        return str_replace('_', '-', $this->snake($value));
    }

    /**
     * Convert string to Title Case headline.
     */
    protected function headline(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', $value);
        $value = preg_replace('/(.)(?=[A-Z])/u', '$1 ', $value);
        return ucwords($value ?? '');
    }

    /**
     * Simple pluralization (adds 's').
     */
    protected function plural(string $value): string
    {
        if (str_ends_with($value, 's')) {
            return $value . 'es';
        }
        if (str_ends_with($value, 'y')) {
            return substr($value, 0, -1) . 'ies';
        }
        return $value . 's';
    }
}

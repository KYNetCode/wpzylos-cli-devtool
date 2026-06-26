<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Support;

use RuntimeException;

/**
 * Context Resolver.
 *
 * Discovers and resolves plugin context from .plugin-config.json files.
 * This enables dynamic value resolution for scaffolding commands without hardcoding.
 *
 * @package WPZylos\Framework\Cli\DevTool\Support
 */
class ContextResolver
{
    /**
     * Configuration filename to search for.
     */
    private const CONFIG_FILE = '.plugin-config.json';

    /**
     * Maximum directory levels to traverse upward.
     */
    private const MAX_DEPTH = 10;

    /**
     * @var string|null Cached config path
     */
    private ?string $configPath = null;

    /**
     * @var array<string, mixed>|null Cached config data
     */
    private ?array $config = null;

    /**
     * Resolve context from the given path.
     *
     * @param string $startPath Starting path for discovery
     *
     * @return array{
     *     namespace: string,
     *     slug: string,
     *     prefix: string,
     *     cssPrefix: string,
     *     textDomain: string,
     *     version: string
     * }
     *
     * @throws RuntimeException If config not found
     */
    public function resolve(string $startPath): array
    {
        $config = $this->loadConfig($startPath);

        return [
            'namespace' => $this->extractNamespace($config),
            'slug' => $this->extractSlug($config),
            'prefix' => $this->extractPrefix($config),
            'cssPrefix' => $this->extractCssPrefix($config),
            'textDomain' => $this->extractTextDomain($config),
            'version' => $this->extractVersion($config),
        ];
    }

    /**
     * Try to resolve context, returning null if not found.
     *
     * @param string $startPath Starting path for discovery
     *
     * @return array{
     *     namespace: string,
     *     slug: string,
     *     prefix: string,
     *     cssPrefix: string,
     *     textDomain: string,
     *     version: string
     * }|null
     */
    public function tryResolve(string $startPath): ?array
    {
        try {
            return $this->resolve($startPath);
        } catch (RuntimeException) {
            return null;
        }
    }

    /**
     * Check if context can be resolved from the given path.
     *
     * @param string $startPath Starting path for discovery
     *
     * @return bool
     */
    public function canResolve(string $startPath): bool
    {
        return $this->findConfigPath($startPath) !== null;
    }

    /**
     * Get the path to the discovered config file.
     *
     * @param string $startPath Starting path for discovery
     *
     * @return string|null
     */
    public function getConfigPath(string $startPath): ?string
    {
        return $this->findConfigPath($startPath);
    }

    /**
     * Load configuration from the nearest .plugin-config.json.
     *
     * @param string $startPath Starting path for discovery
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException If config not found or invalid
     */
    private function loadConfig(string $startPath): array
    {
        if ($this->config !== null && $this->configPath !== null) {
            return $this->config;
        }

        $configPath = $this->findConfigPath($startPath);

        if ($configPath === null) {
            throw new RuntimeException(
                "Could not find " . self::CONFIG_FILE . " in {$startPath} or parent directories. " .
                "Run 'scaffold init' first or provide --namespace option."
            );
        }

        $content = file_get_contents($configPath);

        if ($content === false) {
            throw new RuntimeException("Could not read config file: {$configPath}");
        }

        $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            throw new RuntimeException("Invalid config file format: {$configPath}");
        }

        $this->configPath = $configPath;
        $this->config = $data;

        return $data;
    }

    /**
     * Find the config file path by traversing up from start path.
     *
     * @param string $startPath Starting path for discovery
     *
     * @return string|null
     */
    private function findConfigPath(string $startPath): ?string
    {
        $currentPath = realpath($startPath);

        if ($currentPath === false) {
            $currentPath = $startPath;
        }

        $depth = 0;

        while ($depth < self::MAX_DEPTH) {
            $configPath = $currentPath . DIRECTORY_SEPARATOR . self::CONFIG_FILE;

            if (file_exists($configPath)) {
                return $configPath;
            }

            $parentPath = dirname($currentPath);

            // Reached filesystem root
            if ($parentPath === $currentPath) {
                break;
            }

            $currentPath = $parentPath;
            $depth++;
        }

        return null;
    }

    /**
     * Extract namespace from config.
     *
     * @param array<string, mixed> $config
     *
     * @return string
     */
    private function extractNamespace(array $config): string
    {
        return $config['plugin']['namespace']
            ?? $config['namespace']
            ?? 'MyPlugin';
    }

    /**
     * Extract slug from config.
     *
     * @param array<string, mixed> $config
     *
     * @return string
     */
    private function extractSlug(array $config): string
    {
        return $config['plugin']['slug']
            ?? $config['slug']
            ?? 'my-plugin';
    }

    /**
     * Extract prefix from config.
     *
     * @param array<string, mixed> $config
     *
     * @return string
     */
    private function extractPrefix(array $config): string
    {
        $prefix = $config['plugin']['prefix']
            ?? $config['plugin']['dbPrefix']
            ?? $config['plugin']['scoperPrefix']
            ?? $config['prefix']
            ?? 'mp_';

        // Ensure prefix ends with underscore
        if (!str_ends_with($prefix, '_')) {
            $prefix .= '_';
        }

        return $prefix;
    }

    /**
     * Extract the public CSS class prefix from config.
     *
     * @param array<string, mixed> $config
     */
    private function extractCssPrefix(array $config): string
    {
        $prefix = $config['plugin']['cssPrefix']
            ?? $config['cssPrefix']
            ?? 'mp-';

        if (!str_ends_with($prefix, '-')) {
            $prefix .= '-';
        }

        return $prefix;
    }

    /**
     * Extract text domain from config.
     *
     * @param array<string, mixed> $config
     *
     * @return string
     */
    private function extractTextDomain(array $config): string
    {
        return $config['plugin']['textDomain']
            ?? $config['textDomain']
            ?? $config['plugin']['slug']
            ?? $config['slug']
            ?? 'my-plugin';
    }

    /**
     * Extract version from config.
     *
     * @param array<string, mixed> $config
     *
     * @return string
     */
    private function extractVersion(array $config): string
    {
        return $config['plugin']['version']
            ?? $config['version']
            ?? '1.0.0';
    }

    /**
     * Get the plugin root path (directory containing the config file).
     *
     * @param string $startPath Starting path for discovery
     *
     * @return string|null
     */
    public function getPluginRoot(string $startPath): ?string
    {
        $configPath = $this->findConfigPath($startPath);

        if ($configPath === null) {
            return null;
        }

        return dirname($configPath);
    }
}

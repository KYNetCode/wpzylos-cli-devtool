<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool;

use Composer\Script\Event;

/**
 * Composer Installer for WPZylos CLI DevTool.
 *
 * Automatically creates a root-level `wpzylos` executable file
 * in the consumer project (like Laravel's artisan).
 */
class Installer
{
    /**
     * Handle post-install-cmd event.
     */
    public static function postInstall(Event $event): void
    {
        self::publishExecutable($event);
    }

    /**
     * Handle post-update-cmd event.
     */
    public static function postUpdate(Event $event): void
    {
        self::publishExecutable($event);
    }

    /**
     * Publish the wpzylos executable to the project root.
     */
    protected static function publishExecutable(Event $event): void
    {
        $io = $event->getIO();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $projectRoot = dirname($vendorDir);
        $targetPath = $projectRoot . DIRECTORY_SEPARATOR . 'wpzylos';

        // Don't overwrite if it already exists
        if (file_exists($targetPath)) {
            $io->write('<info>wpzylos:</info> Root executable already exists, skipping.');
            return;
        }

        $content = self::getExecutableContent();

        if (file_put_contents($targetPath, $content) !== false) {
            // Make executable on Unix systems
            if (DIRECTORY_SEPARATOR !== '\\') {
                chmod($targetPath, 0755);
            }
            $io->write('<info>wpzylos:</info> Created root executable. Use: <comment>php wpzylos [command]</comment>');
        } else {
            $io->writeError('<error>wpzylos:</error> Failed to create root executable.');
        }
    }

    /**
     * Get the content for the root executable file.
     */
    protected static function getExecutableContent(): string
    {
        return <<<'PHP'
#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * WPZylos CLI Tool
 *
 * Project root executable - forwards to vendor package.
 *
 * @see https://wpzylos.com/docs/latest/packages/wpzylos-cli-devtool
 */

$vendorBin = __DIR__ . '/vendor/bin/wpzylos';

if (!file_exists($vendorBin)) {
    fwrite(STDERR, "WPZylos CLI not found. Run: composer require --dev KYNetCode/wpzylos-cli-devtool\n");
    exit(1);
}

// Pass all arguments to the vendor binary
$args = array_slice($argv, 1);
$command = PHP_BINARY . ' ' . escapeshellarg($vendorBin) . ' ' . implode(' ', array_map('escapeshellarg', $args));

// Execute and pass through exit code
passthru($command, $exitCode);
exit($exitCode);

PHP;
    }
}

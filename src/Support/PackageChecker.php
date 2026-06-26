<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Support;

/**
 * Package Checker.
 *
 * Verifies if required WPZylos packages are installed before running commands.
 * Provides graceful failure with helpful installation instructions.
 *
 * @package WPZylos\Framework\Cli\DevTool\Support
 */
class PackageChecker
{
    /**
     * Package to class mapping for detection.
     *
     * @var array<string, string>
     */
    private const PACKAGE_CLASSES = [
        'wpzylos-database'     => 'WPZylos\\Framework\\Database\\Connection',
        'wpzylos-model'        => 'WPZylos\\Framework\\Model\\Model',
        'wpzylos-queue'        => 'WPZylos\\Framework\\Queue\\Job',
        'wpzylos-mail'         => 'WPZylos\\Framework\\Mail\\Mailable',
        'wpzylos-notification' => 'WPZylos\\Framework\\Notification\\Notification',
        'wpzylos-scheduler'    => 'WPZylos\\Framework\\Scheduler\\Schedule',
        'wpzylos-assets'       => 'WPZylos\\Framework\\Assets\\AssetManager',
        'wpzylos-events'       => 'WPZylos\\Framework\\Events\\EventDispatcher',
        'wpzylos-http'         => 'WPZylos\\Framework\\Http\\Request',
        'wpzylos-routing'      => 'WPZylos\\Framework\\Routing\\Router',
        'wpzylos-views'        => 'WPZylos\\Framework\\Views\\View',
        'wpzylos-validation'   => 'WPZylos\\Framework\\Validation\\Validator',
        'wpzylos-config'       => 'WPZylos\\Framework\\Config\\Config',
        'wpzylos-container'    => 'WPZylos\\Framework\\Container\\Container',
        'wpzylos-logger'       => 'WPZylos\\Framework\\Logger\\Logger',
        'wpzylos-security'     => 'WPZylos\\Framework\\Security\\Nonce',
        'wpzylos-hooks'        => 'WPZylos\\Framework\\Hooks\\HookManager',
        'wpzylos-i18n'         => 'WPZylos\\Framework\\I18n\\I18n',
        'wpzylos-migrations'   => 'WPZylos\\Framework\\Migrations\\Migrator',
    ];

    /**
     * Full composer package names.
     *
     * @var array<string, string>
     */
    private const COMPOSER_PACKAGES = [
        'wpzylos-database'     => 'KYNetCode/wpzylos-database',
        'wpzylos-model'        => 'KYNetCode/wpzylos-model',
        'wpzylos-queue'        => 'KYNetCode/wpzylos-queue',
        'wpzylos-mail'         => 'KYNetCode/wpzylos-mail',
        'wpzylos-notification' => 'KYNetCode/wpzylos-notification',
        'wpzylos-scheduler'    => 'KYNetCode/wpzylos-scheduler',
        'wpzylos-assets'       => 'KYNetCode/wpzylos-assets',
        'wpzylos-events'       => 'KYNetCode/wpzylos-events',
        'wpzylos-http'         => 'KYNetCode/wpzylos-http',
        'wpzylos-routing'      => 'KYNetCode/wpzylos-routing',
        'wpzylos-views'        => 'KYNetCode/wpzylos-views',
        'wpzylos-validation'   => 'KYNetCode/wpzylos-validation',
        'wpzylos-config'       => 'KYNetCode/wpzylos-config',
        'wpzylos-container'    => 'KYNetCode/wpzylos-container',
        'wpzylos-logger'       => 'KYNetCode/wpzylos-logger',
        'wpzylos-security'     => 'KYNetCode/wpzylos-security',
        'wpzylos-hooks'        => 'KYNetCode/wpzylos-hooks',
        'wpzylos-i18n'         => 'KYNetCode/wpzylos-i18n',
        'wpzylos-migrations'   => 'KYNetCode/wpzylos-migrations',
    ];

    /**
     * Check if a package is available.
     *
     * @param string $package Package short name (e.g., 'wpzylos-database')
     *
     * @return bool
     */
    public function isAvailable(string $package): bool
    {
        // First try class existence check
        if (isset(self::PACKAGE_CLASSES[$package])) {
            if (class_exists(self::PACKAGE_CLASSES[$package])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check multiple packages and return missing ones.
     *
     * @param string[] $packages Package short names
     *
     * @return string[] Missing packages
     */
    public function getMissing(array $packages): array
    {
        $missing = [];

        foreach ($packages as $package) {
            if (!$this->isAvailable($package)) {
                $missing[] = $package;
            }
        }

        return $missing;
    }

    /**
     * Check if all required packages are available.
     *
     * @param string[] $packages Package short names
     *
     * @return bool
     */
    public function allAvailable(array $packages): bool
    {
        return count($this->getMissing($packages)) === 0;
    }

    /**
     * Get the install command for a package.
     *
     * @param string $package Package short name
     *
     * @return string
     */
    public function getInstallCommand(string $package): string
    {
        $composerPackage = self::COMPOSER_PACKAGES[$package] ?? "KYNetCode/{$package}";

        return "composer require {$composerPackage}";
    }

    /**
     * Get a user-friendly error message for missing packages.
     *
     * @param string[] $packages Missing package short names
     *
     * @return string
     */
    public function getMissingPackageMessage(array $packages): string
    {
        if (count($packages) === 0) {
            return '';
        }

        if (count($packages) === 1) {
            $package = $packages[0];
            $composerPackage = self::COMPOSER_PACKAGES[$package] ?? "KYNetCode/{$package}";

            return sprintf(
                "This command requires the '%s' package which is not installed.\n" .
                "Install it with: %s",
                $package,
                $this->getInstallCommand($package)
            );
        }

        $lines = ["This command requires the following packages which are not installed:"];

        foreach ($packages as $package) {
            $lines[] = "  - {$package}";
        }

        $lines[] = "";
        $lines[] = "Install them with:";

        foreach ($packages as $package) {
            $lines[] = "  " . $this->getInstallCommand($package);
        }

        return implode("\n", $lines);
    }

    /**
     * Validate packages and throw if any are missing.
     *
     * @param string[] $packages Package short names
     *
     * @return void
     *
     * @throws \RuntimeException If packages are missing
     */
    public function requirePackages(array $packages): void
    {
        $missing = $this->getMissing($packages);

        if (count($missing) > 0) {
            throw new \RuntimeException($this->getMissingPackageMessage($missing));
        }
    }

    /**
     * Get the marker class for a package.
     *
     * @param string $package Package short name
     *
     * @return string|null
     */
    public function getPackageClass(string $package): ?string
    {
        return self::PACKAGE_CLASSES[$package] ?? null;
    }
}

<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeControllerCommand;

/**
 * Tests for MakeControllerCommand.
 */
class MakeControllerCommandTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/wpzylos-test-' . uniqid();
        mkdir($this->tempDir . '/app/Http/Controllers', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    public function testCommandIsInstantiable(): void
    {
        $command = new MakeControllerCommand();
        $this->assertInstanceOf(MakeControllerCommand::class, $command);
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeControllerCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasPathOption(): void
    {
        $command = new MakeControllerCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
    }

    public function testCommandHasNamespaceOption(): void
    {
        $command = new MakeControllerCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('namespace'));
    }

    public function testDefaultNamespaceIsMyPlugin(): void
    {
        $command = new MakeControllerCommand();
        $definition = $command->getDefinition();

        $this->assertSame('MyPlugin', $definition->getOption('namespace')->getDefault());
    }

    public function testDefaultPathIsCurrentWorkingDirectory(): void
    {
        $command = new MakeControllerCommand();
        $definition = $command->getDefinition();

        $this->assertSame(getcwd(), $definition->getOption('path')->getDefault());
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}

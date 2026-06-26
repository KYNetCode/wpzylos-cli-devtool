<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeMigrationCommand;

/**
 * Tests for MakeMigrationCommand.
 */
class MakeMigrationCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeMigrationCommand();
        $this->assertInstanceOf(MakeMigrationCommand::class, $command);
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeMigrationCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasPathOption(): void
    {
        $command = new MakeMigrationCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
    }

    public function testCommandHasNamespaceOption(): void
    {
        $command = new MakeMigrationCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('namespace'));
    }

    public function testCommandHasPrefixOption(): void
    {
        $command = new MakeMigrationCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('prefix'));
    }

    public function testDefaultNamespaceIsMyPlugin(): void
    {
        $command = new MakeMigrationCommand();
        $definition = $command->getDefinition();

        $this->assertSame('MyPlugin', $definition->getOption('namespace')->getDefault());
    }

    public function testDefaultPrefixIsMp(): void
    {
        $command = new MakeMigrationCommand();
        $definition = $command->getDefinition();

        $this->assertSame('mp_', $definition->getOption('prefix')->getDefault());
    }
}

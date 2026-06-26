<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeRequestCommand;

/**
 * Tests for MakeRequestCommand.
 */
class MakeRequestCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeRequestCommand();
        $this->assertInstanceOf(MakeRequestCommand::class, $command);
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeRequestCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasPathOption(): void
    {
        $command = new MakeRequestCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
    }

    public function testCommandHasNamespaceOption(): void
    {
        $command = new MakeRequestCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('namespace'));
    }

    public function testDefaultNamespaceIsMyPlugin(): void
    {
        $command = new MakeRequestCommand();
        $definition = $command->getDefinition();

        $this->assertSame('MyPlugin', $definition->getOption('namespace')->getDefault());
    }

    public function testDefaultPathIsCurrentWorkingDirectory(): void
    {
        $command = new MakeRequestCommand();
        $definition = $command->getDefinition();

        $this->assertSame(getcwd(), $definition->getOption('path')->getDefault());
    }
}

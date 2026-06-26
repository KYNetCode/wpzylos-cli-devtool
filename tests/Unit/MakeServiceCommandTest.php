<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeServiceCommand;

/**
 * Tests for MakeServiceCommand.
 */
class MakeServiceCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeServiceCommand();
        $this->assertInstanceOf(MakeServiceCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeServiceCommand();
        $this->assertSame('make:service', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeServiceCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasPathOption(): void
    {
        $command = new MakeServiceCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
    }

    public function testCommandHasNamespaceOption(): void
    {
        $command = new MakeServiceCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('namespace'));
    }

    public function testCommandHasForceOption(): void
    {
        $command = new MakeServiceCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('force'));
    }

    public function testCommandHasDryRunOption(): void
    {
        $command = new MakeServiceCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeActionCommand;

/**
 * Tests for MakeActionCommand.
 */
class MakeActionCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeActionCommand();
        $this->assertInstanceOf(MakeActionCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeActionCommand();
        $this->assertSame('make:action', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeActionCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasHookOption(): void
    {
        $command = new MakeActionCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('hook'));
        $this->assertSame('init', $definition->getOption('hook')->getDefault());
    }

    public function testCommandHasPriorityOption(): void
    {
        $command = new MakeActionCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('priority'));
        $this->assertSame('10', $definition->getOption('priority')->getDefault());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeActionCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

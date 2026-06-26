<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeEnumCommand;

/**
 * Tests for MakeEnumCommand.
 */
class MakeEnumCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeEnumCommand();
        $this->assertInstanceOf(MakeEnumCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeEnumCommand();
        $this->assertSame('make:enum', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeEnumCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasIntOption(): void
    {
        $command = new MakeEnumCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('int'));
    }

    public function testCommandHasPlainOption(): void
    {
        $command = new MakeEnumCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('plain'));
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeEnumCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

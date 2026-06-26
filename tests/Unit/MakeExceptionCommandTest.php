<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeExceptionCommand;

/**
 * Tests for MakeExceptionCommand.
 */
class MakeExceptionCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeExceptionCommand();
        $this->assertInstanceOf(MakeExceptionCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeExceptionCommand();
        $this->assertSame('make:exception', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeExceptionCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasRenderOption(): void
    {
        $command = new MakeExceptionCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('render'));
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeExceptionCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

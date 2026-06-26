<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeShortcodeCommand;

/**
 * Tests for MakeShortcodeCommand.
 */
class MakeShortcodeCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeShortcodeCommand();
        $this->assertInstanceOf(MakeShortcodeCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeShortcodeCommand();
        $this->assertSame('make:shortcode', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeShortcodeCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasTagOption(): void
    {
        $command = new MakeShortcodeCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('tag'));
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeShortcodeCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

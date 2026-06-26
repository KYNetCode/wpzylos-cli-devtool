<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeMailCommand;

/**
 * Tests for MakeMailCommand.
 */
class MakeMailCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeMailCommand();
        $this->assertInstanceOf(MakeMailCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeMailCommand();
        $this->assertSame('make:mail', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeMailCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeMailCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

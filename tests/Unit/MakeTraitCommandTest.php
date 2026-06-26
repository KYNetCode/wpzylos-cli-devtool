<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeTraitCommand;

/**
 * Tests for MakeTraitCommand.
 */
class MakeTraitCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeTraitCommand();
        $this->assertInstanceOf(MakeTraitCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeTraitCommand();
        $this->assertSame('make:trait', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeTraitCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeTraitCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeRuleCommand;

/**
 * Tests for MakeRuleCommand.
 */
class MakeRuleCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeRuleCommand();
        $this->assertInstanceOf(MakeRuleCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeRuleCommand();
        $this->assertSame('make:rule', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeRuleCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeRuleCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

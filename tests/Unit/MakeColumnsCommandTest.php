<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeColumnsCommand;

/**
 * Tests for MakeColumnsCommand.
 */
class MakeColumnsCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeColumnsCommand();
        $this->assertInstanceOf(MakeColumnsCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeColumnsCommand();
        $this->assertSame('make:columns', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeColumnsCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasPostTypeOption(): void
    {
        $command = new MakeColumnsCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('post-type'));
        $this->assertSame('post', $definition->getOption('post-type')->getDefault());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeColumnsCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

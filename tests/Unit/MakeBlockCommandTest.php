<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeBlockCommand;

/**
 * Tests for MakeBlockCommand.
 */
class MakeBlockCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeBlockCommand();
        $this->assertInstanceOf(MakeBlockCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeBlockCommand();
        $this->assertSame('make:block', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeBlockCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }

    public function testCommandHasTitleOption(): void
    {
        $command = new MakeBlockCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('title'));
    }

    public function testCommandHasDescriptionOption(): void
    {
        $command = new MakeBlockCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('description'));
        $this->assertSame('A custom block.', $definition->getOption('description')->getDefault());
    }

    public function testCommandHasIconOption(): void
    {
        $command = new MakeBlockCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('icon'));
        $this->assertSame('block-default', $definition->getOption('icon')->getDefault());
    }

    public function testCommandHasCategoryOption(): void
    {
        $command = new MakeBlockCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('category'));
        $this->assertSame('widgets', $definition->getOption('category')->getDefault());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeBlockCommand();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('path'));
        $this->assertTrue($definition->hasOption('namespace'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('dry-run'));
    }
}

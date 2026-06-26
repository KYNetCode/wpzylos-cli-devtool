<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeTestCommand;

class MakeTestCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeTestCommand();
        $this->assertInstanceOf(MakeTestCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeTestCommand();
        $this->assertSame('make:test', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeTestCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasFeatureOption(): void
    {
        $command = new MakeTestCommand();
        $this->assertTrue($command->getDefinition()->hasOption('feature'));
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeTestCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

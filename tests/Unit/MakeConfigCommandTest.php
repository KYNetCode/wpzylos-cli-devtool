<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeConfigCommand;

class MakeConfigCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeConfigCommand();
        $this->assertInstanceOf(MakeConfigCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeConfigCommand();
        $this->assertSame('make:config', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeConfigCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeConfigCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeCommandCommand;

class MakeCommandCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeCommandCommand();
        $this->assertInstanceOf(MakeCommandCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeCommandCommand();
        $this->assertSame('make:command', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeCommandCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeCommandCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

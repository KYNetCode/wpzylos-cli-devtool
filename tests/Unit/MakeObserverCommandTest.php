<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeObserverCommand;

class MakeObserverCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeObserverCommand();
        $this->assertInstanceOf(MakeObserverCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeObserverCommand();
        $this->assertSame('make:observer', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeObserverCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasModelOption(): void
    {
        $command = new MakeObserverCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('model'));
        $this->assertSame('m', $def->getOption('model')->getShortcut());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeObserverCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

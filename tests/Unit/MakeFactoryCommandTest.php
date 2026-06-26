<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeFactoryCommand;

class MakeFactoryCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeFactoryCommand();
        $this->assertInstanceOf(MakeFactoryCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeFactoryCommand();
        $this->assertSame('make:factory', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeFactoryCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasModelOption(): void
    {
        $command = new MakeFactoryCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('model'));
        $this->assertSame('m', $def->getOption('model')->getShortcut());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeFactoryCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

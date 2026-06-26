<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeScopeCommand;

class MakeScopeCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeScopeCommand();
        $this->assertInstanceOf(MakeScopeCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeScopeCommand();
        $this->assertSame('make:scope', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeScopeCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeScopeCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

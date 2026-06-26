<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeRestCommand;

class MakeRestCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeRestCommand();
        $this->assertInstanceOf(MakeRestCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeRestCommand();
        $this->assertSame('make:rest', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeRestCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasRouteOption(): void
    {
        $command = new MakeRestCommand();
        $this->assertTrue($command->getDefinition()->hasOption('route'));
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeRestCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

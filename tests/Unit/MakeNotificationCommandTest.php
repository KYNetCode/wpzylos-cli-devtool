<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeNotificationCommand;

class MakeNotificationCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeNotificationCommand();
        $this->assertInstanceOf(MakeNotificationCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeNotificationCommand();
        $this->assertSame('make:notification', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeNotificationCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeNotificationCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

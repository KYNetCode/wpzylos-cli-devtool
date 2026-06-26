<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeCronCommand;

class MakeCronCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeCronCommand();
        $this->assertInstanceOf(MakeCronCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeCronCommand();
        $this->assertSame('make:cron', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeCronCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasRecurrenceOption(): void
    {
        $command = new MakeCronCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('recurrence'));
        $this->assertSame('daily', $def->getOption('recurrence')->getDefault());
        $this->assertSame('r', $def->getOption('recurrence')->getShortcut());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeCronCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeScheduleCommand;

class MakeScheduleCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeScheduleCommand();
        $this->assertInstanceOf(MakeScheduleCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeScheduleCommand();
        $this->assertSame('make:schedule', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeScheduleCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasFrequencyOption(): void
    {
        $command = new MakeScheduleCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('frequency'));
        $this->assertSame('daily', $def->getOption('frequency')->getDefault());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeScheduleCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

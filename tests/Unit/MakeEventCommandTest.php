<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeEventCommand;

class MakeEventCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeEventCommand();
        $this->assertInstanceOf(MakeEventCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeEventCommand();
        $this->assertSame('make:event', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeEventCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeEventCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

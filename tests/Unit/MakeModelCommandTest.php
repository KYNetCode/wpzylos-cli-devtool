<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeModelCommand;

class MakeModelCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeModelCommand();
        $this->assertInstanceOf(MakeModelCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeModelCommand();
        $this->assertSame('make:model', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeModelCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeModelCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

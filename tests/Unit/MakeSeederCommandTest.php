<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeSeederCommand;

class MakeSeederCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeSeederCommand();
        $this->assertInstanceOf(MakeSeederCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeSeederCommand();
        $this->assertSame('make:seeder', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeSeederCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeSeederCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

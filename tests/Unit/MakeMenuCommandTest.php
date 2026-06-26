<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeMenuCommand;

class MakeMenuCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeMenuCommand();
        $this->assertInstanceOf(MakeMenuCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeMenuCommand();
        $this->assertSame('make:menu', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeMenuCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeMenuCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

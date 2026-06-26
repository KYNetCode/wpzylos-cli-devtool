<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakePolicyCommand;

class MakePolicyCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakePolicyCommand();
        $this->assertInstanceOf(MakePolicyCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakePolicyCommand();
        $this->assertSame('make:policy', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakePolicyCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakePolicyCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

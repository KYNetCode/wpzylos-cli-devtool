<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeProviderCommand;

class MakeProviderCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeProviderCommand();
        $this->assertInstanceOf(MakeProviderCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeProviderCommand();
        $this->assertSame('make:provider', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeProviderCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeProviderCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

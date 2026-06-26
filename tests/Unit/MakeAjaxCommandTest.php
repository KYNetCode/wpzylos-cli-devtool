<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeAjaxCommand;

class MakeAjaxCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeAjaxCommand();
        $this->assertInstanceOf(MakeAjaxCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeAjaxCommand();
        $this->assertSame('make:ajax', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeAjaxCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasPublicOption(): void
    {
        $command = new MakeAjaxCommand();
        $this->assertTrue($command->getDefinition()->hasOption('public'));
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeAjaxCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

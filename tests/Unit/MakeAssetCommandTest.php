<?php

defined('ABSPATH') || exit;

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Cli\DevTool\Commands\MakeAssetCommand;

class MakeAssetCommandTest extends TestCase
{
    public function testCommandIsInstantiable(): void
    {
        $command = new MakeAssetCommand();
        $this->assertInstanceOf(MakeAssetCommand::class, $command);
    }

    public function testCommandName(): void
    {
        $command = new MakeAssetCommand();
        $this->assertSame('make:asset', $command->getName());
    }

    public function testCommandHasRequiredArguments(): void
    {
        $command = new MakeAssetCommand();
        $this->assertTrue($command->getDefinition()->hasArgument('name'));
        $this->assertTrue($command->getDefinition()->getArgument('name')->isRequired());
    }

    public function testCommandHasAdminOption(): void
    {
        $command = new MakeAssetCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('admin'));
    }

    public function testCommandHasBaseOptions(): void
    {
        $command = new MakeAssetCommand();
        $def = $command->getDefinition();
        $this->assertTrue($def->hasOption('path'));
        $this->assertTrue($def->hasOption('force'));
        $this->assertTrue($def->hasOption('dry-run'));
    }
}

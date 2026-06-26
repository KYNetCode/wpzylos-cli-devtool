<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Notification Command.
 *
 * Creates a multi-channel notification class supporting WordPress
 * wp_mail and admin notices channels.
 *
 * Usage:
 *   wpzylos make:notification OrderShipped
 *   wpzylos make:notification Notifications/InvoicePaid
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeNotificationCommand extends BaseGeneratorCommand
{
    /** @inheritDoc */
    protected string $type = 'Notification';

    /** @inheritDoc */
    protected array $requiredPackages = ['wpzylos-notification'];

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:notification')
            ->setDescription('Create a new multi-channel notification')
            ->setHelp(
                'This command creates a notification class with WordPress channels:' . PHP_EOL .
                'email (wp_mail) and admin notices. Additional channels can be added.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:notification OrderShipped</comment>' . PHP_EOL .
                '  <comment>wpzylos make:notification Notifications/InvoicePaid</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Notification class name (e.g., OrderShipped)');
    }

    /** @inheritDoc */
    protected function getStub(InputInterface $input): string
    {
        return 'notification';
    }

    /** @inheritDoc */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Notifications';
    }

    /** @inheritDoc */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Notifications';
    }

    /** @inheritDoc */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln('');
        $output->writeln('<info>Notification Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>Channels:</comment>  mail, admin_notice');
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  <comment>$notification = new \\' . $qualifiedName . '([');
        $output->writeln('      \'subject\' => \'Order Shipped\',');
        $output->writeln('      \'message\' => \'Your order has been shipped.\',');
        $output->writeln('  ]);</comment>');
        $output->writeln('  <comment>$notification->send(\'user@example.com\');</comment>');
    }
}

<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Make Mail Command.
 *
 * Creates a new mailable notification class that uses wp_mail()
 * with a fluent build/content/send API.
 *
 * Usage:
 *   wpzylos make:mail WelcomeEmail
 *   wpzylos make:mail Orders/OrderConfirmation
 *
 * @package WPZylos\Framework\Cli\DevTool\Commands
 */
class MakeMailCommand extends BaseGeneratorCommand
{
    /**
     * @inheritDoc
     */
    protected string $type = 'Mail';

    /**
     * @inheritDoc
     */
    protected array $requiredPackages = ['wpzylos-mail'];

    /**
     * Configure the command.
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('make:mail')
            ->setDescription('Create a new mailable notification class')
            ->setHelp(
                'This command creates a mailable notification class that wraps wp_mail()' . PHP_EOL .
                'with a clean, fluent API. The generated class includes build(), content(),' . PHP_EOL .
                'and send() methods for composing and dispatching emails.' . PHP_EOL .
                PHP_EOL .
                '<info>Examples:</info>' . PHP_EOL .
                '  <comment>wpzylos make:mail WelcomeEmail</comment>' . PHP_EOL .
                '  <comment>wpzylos make:mail Orders/OrderConfirmation</comment>'
            )
            ->addArgument('name', InputArgument::REQUIRED, 'Mailable class name (e.g., WelcomeEmail)');
    }

    /**
     * @inheritDoc
     */
    protected function getStub(InputInterface $input): string
    {
        return 'mail';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespaceSegment(): string
    {
        return 'Mail';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultOutputDirectory(): string
    {
        return 'app/Mail';
    }

    /**
     * @inheritDoc
     */
    protected function afterGeneration(
        string $qualifiedName,
        array $nameData,
        string $filePath,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $output->writeln('');
        $output->writeln('<info>Mail Details:</info>');
        $output->writeln('  <comment>Class:</comment>     ' . $nameData['class']);
        $output->writeln('  <comment>Namespace:</comment> ' . $this->getNamespace($qualifiedName));
        $output->writeln('  <comment>File:</comment>      ' . $this->getRelativePath($filePath));

        $output->writeln('');
        $output->writeln('<info>Usage:</info>');
        $output->writeln('  Send a mail:');
        $output->writeln('  <comment>$mail = new \\' . $qualifiedName . '(\'user@example.com\');</comment>');
        $output->writeln('  <comment>$mail->send();</comment>');
    }
}

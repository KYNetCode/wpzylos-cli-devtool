<?php

declare(strict_types=1);

namespace WPZylos\Framework\Cli\DevTool;

use Symfony\Component\Console\Application as BaseApplication;

/**
 * WPZylos CLI Application
 *
 * Custom Symfony Console Application with a branded header.
 */
class Application extends BaseApplication
{
    public const NAME    = 'WPZylos CLI';
    public const VERSION = '1.0.0';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
    }

    /**
     * Returns the banner shown at the top of the list/help output.
     */
    public function getHelp(): string
    {
        return implode("\n", [
            '',
            '<fg=blue>  ██╗    ██╗██████╗ ███████╗██╗   ██╗██╗      ██████╗ ███████╗</>',
            '<fg=blue>  ██║    ██║██╔══██╗╚══███╔╝╚██╗ ██╔╝██║     ██╔═══██╗██╔════╝</>',
            '<fg=blue>  ██║ █╗ ██║██████╔╝  ███╔╝  ╚████╔╝ ██║     ██║   ██║███████╗</>',
            '<fg=blue>  ██║███╗██║██╔═══╝  ███╔╝    ╚██╔╝  ██║     ██║   ██║╚════██║</>',
            '<fg=blue>  ╚███╔███╔╝██║     ███████╗   ██║   ███████╗╚██████╔╝███████║</>',
            '<fg=blue>   ╚══╝╚══╝ ╚═╝     ╚══════╝   ╚═╝   ╚══════╝ ╚═════╝ ╚══════╝</>',
            '',
            '  <fg=gray>WordPress Plugin Development Toolkit</>  <fg=yellow>v' . self::VERSION . '</>',
            '',
        ]);
    }
}

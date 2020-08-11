<?php

namespace VendorFPF\WPDesk\Composer\Codeception\Commands;

use VendorFPF\Composer\Command\BaseCommand as CodeceptionBaseCommand;
use VendorFPF\Symfony\Component\Console\Output\OutputInterface;
/**
 * Base for commands - declares common methods.
 *
 * @package WPDesk\Composer\Codeception\Commands
 */
abstract class BaseCommand extends \VendorFPF\Composer\Command\BaseCommand
{
    /**
     * @param string $command
     * @param OutputInterface $output
     */
    protected function execAndOutput($command, \VendorFPF\Symfony\Component\Console\Output\OutputInterface $output)
    {
        \passthru($command);
    }
}

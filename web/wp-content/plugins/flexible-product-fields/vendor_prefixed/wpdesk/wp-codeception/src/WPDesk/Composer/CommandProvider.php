<?php

namespace VendorFPF\WPDesk\Composer\Codeception;

use VendorFPF\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests;
use VendorFPF\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests;
/**
 * Links plugin commands handlers to composer.
 */
class CommandProvider implements \VendorFPF\Composer\Plugin\Capability\CommandProvider
{
    public function getCommands()
    {
        return [new \VendorFPF\WPDesk\Composer\Codeception\Commands\CreateCodeceptionTests(), new \VendorFPF\WPDesk\Composer\Codeception\Commands\RunCodeceptionTests()];
    }
}

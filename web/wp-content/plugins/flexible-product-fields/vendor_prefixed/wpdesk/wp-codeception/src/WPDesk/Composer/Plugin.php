<?php

namespace VendorFPF\WPDesk\Composer\Codeception;

use VendorFPF\Composer\Composer;
use VendorFPF\Composer\IO\IOInterface;
use VendorFPF\Composer\Plugin\Capable;
use VendorFPF\Composer\Plugin\PluginInterface;
/**
 * Composer plugin.
 *
 * @package WPDesk\Composer\Codeception
 */
class Plugin implements \VendorFPF\Composer\Plugin\PluginInterface, \VendorFPF\Composer\Plugin\Capable
{
    /**
     * @var Composer
     */
    private $composer;
    /**
     * @var IOInterface
     */
    private $io;
    public function activate(\VendorFPF\Composer\Composer $composer, \VendorFPF\Composer\IO\IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
    public function getCapabilities()
    {
        return [\VendorFPF\Composer\Plugin\Capability\CommandProvider::class => \VendorFPF\WPDesk\Composer\Codeception\CommandProvider::class];
    }
}

<?php

namespace VendorFPF\WPDesk\PluginBuilder\Storage;

use VendorFPF\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
interface PluginStorage
{
    /**
     * @param string $class
     * @param AbstractPlugin $object
     */
    public function add_to_storage($class, $object);
    /**
     * @param string $class
     *
     * @return AbstractPlugin
     */
    public function get_from_storage($class);
}

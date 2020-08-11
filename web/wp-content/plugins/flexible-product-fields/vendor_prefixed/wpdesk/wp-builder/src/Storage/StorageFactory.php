<?php

namespace VendorFPF\WPDesk\PluginBuilder\Storage;

class StorageFactory
{
    /**
     * @return PluginStorage
     */
    public function create_storage()
    {
        return new \VendorFPF\WPDesk\PluginBuilder\Storage\WordpressFilterStorage();
    }
}

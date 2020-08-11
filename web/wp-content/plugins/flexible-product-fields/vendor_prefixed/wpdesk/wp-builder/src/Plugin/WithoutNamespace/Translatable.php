<?php

namespace VendorFPF;

if (!\interface_exists('VendorFPF\\WPDesk_Translable')) {
    require_once 'Translable.php';
}
/**
 * Have info about textdomain - how to translate texts
 *
 * have to be compatible with PHP 5.2.x
 */
interface WPDesk_Translatable extends \VendorFPF\WPDesk_Translable
{
    /** @return string */
    public function get_text_domain();
}

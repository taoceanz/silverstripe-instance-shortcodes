<?php

namespace TAOCEANZ\ORM;

use TAOCEANZ\InstanceShortcodes;
use SilverStripe\ORM\DataExtension;

/**
 * Extension to augment given properties on a given object
 *
 * @author taoceanz <do@taocean.io>
 */
class InstanceShortcodesDataExtension extends DataExtension
{
    use InstanceShortcodes;
}

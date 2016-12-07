<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Config;


/**
 * View helper class for retrieving configuration values.
 *
 * @package MW
 * @subpackage View
 */
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the config value.
	 *
	 * @param string|null $name Name of the config key or null for all parameters
	 * @param mixed $default Default value if config key is not available
	 * @return mixed Config value or associative list of key/value pairs
	 */
	public function transform( $name = null, $default = null );
}
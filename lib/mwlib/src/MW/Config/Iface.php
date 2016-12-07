<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config;


/**
 * Generic minimal interface for configuration setting classes
 *
 * @package MW
 * @subpackage Config
 */
interface Iface
{
	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get($name, $default = null);

	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 * @return null
	 */
	public function set($name, $value);
}
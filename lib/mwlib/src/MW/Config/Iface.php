<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	public function get( string $name, $default = null );

	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 * @return \Aimeos\MW\Config\Iface Config instance for method chaining
	 */
	public function set( string $name, $value ) : Iface;
}

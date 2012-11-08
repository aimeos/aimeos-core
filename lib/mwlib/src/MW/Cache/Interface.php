<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Cache
 * @version $$
 */


/**
 * Generic minimal interface for cacheing classes.
 *
 * @package MW
 * @subpackage Cache
 */
interface MW_Cache_Interface
{
	/**
	 * Tests if caching is available.
	 *
	 * @return boolean True if available, false if not
	 */
	public function isAvailable();

	/**
	 * Returns the value of the requested cache key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( $name, $default = null );

	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 */
	public function set( $name, $value );
}
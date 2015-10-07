<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Session
 */


namespace Aimeos\MW\Session;


/**
 * Implementation without using permanent session.
 *
 * @package MW
 * @subpackage Session
 */
class None implements \Aimeos\MW\Session\Iface
{
	private $data = array();


	/**
	 * Returns the value of the requested session key.
	 *
	 * @param string $name Key of the requested value in the session
	 * @param string|null $default Value returned if requested key isn't found
	 * @return string Value associated to the requested key
	 */
	public function get( $name, $default = null )
	{
		if( array_key_exists( $name, $this->data ) !== false ) {
			return $this->data[$name];
		}

		return $default;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Key to the value which should be stored in the session
	 * @param string $value Value that should be associated with the given key
	 */
	public function set( $name, $value )
	{
		$this->data[$name] = $value;
	}
}

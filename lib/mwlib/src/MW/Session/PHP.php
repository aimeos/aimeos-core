<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Session
 */


namespace Aimeos\MW\Session;


/**
 * Implementation using PHP session.
 *
 * @package MW
 * @subpackage Session
 */
class PHP extends Base implements \Aimeos\MW\Session\Iface
{
	/**
	 * Remove the given key from the session.
	 *
	 * @param string $name Key of the requested value in the session
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function del( string $name ) : Iface
	{
		unset( $_SESSION[$name] );
		return $this;
	}


	/**
	 * Returns the value of the requested session key.
	 *
	 * If the returned value wasn't a string, it's decoded from its JSON
	 * representation.
	 *
	 * @param string $name Key of the requested value in the session
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( string $name, $default = null )
	{
		if( isset( $_SESSION[$name] ) ) {
			return $_SESSION[$name];
		}

		return $default;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * If the value isn't a string, it's encoded into a JSON representation and
	 * decoded again when using the get() method.
	 *
	 * @param string $name Key to the value which should be stored in the session
	 * @param mixed $value Value that should be associated with the given key
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function set( string $name, $value ) : Iface
	{
		$_SESSION[$name] = $value;
		return $this;
	}
}

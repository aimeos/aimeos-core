<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MW
 * @subpackage Session
 */


namespace Aimeos\MW\Session;


/**
 * Base class for session adapters
 *
 * @package MW
 * @subpackage Session
 */
abstract class Base implements \Aimeos\MW\Session\Iface
{
	/**
	 * Sets a list of key/value pairs.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function apply( array $values ) : Iface
	{
		foreach( $values as $key => $value ) {
			$this->set( $key, $value );
		}

		return $this;
	}


	/**
	 * Returns the value of the requested session key and remove it from the session.
	 *
	 * If the returned value wasn't a string, it's decoded from its serialized
	 * representation.
	 *
	 * @param string $name Key of the requested value in the session
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function pull( string $name, $default = null )
	{
		$value = $this->get( $name, $default );
		$this->del( $name );

		return $value;
	}


	/**
	 * Remove the list of keys from the session.
	 *
	 * @param array $name Keys to remove from the session
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function remove( array $names ) : Iface
	{
		foreach( $names as $name ) {
			$this->del( $name );
		}

		return $this;
	}
}

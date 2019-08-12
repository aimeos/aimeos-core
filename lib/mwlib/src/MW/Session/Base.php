<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
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
abstract class Base
{
	/**
	 * Sets a list of key/value pairs.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @return \Aimeos\MW\Session\Iface Session instance for method chaining
	 */
	public function apply( array $values )
	{
		foreach( $values as $key => $value ) {
			$this->set( $key, $value );
		}

		return $this;
	}
}

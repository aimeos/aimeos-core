<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Session
 */


/**
 * Implementation using PHP session.
 *
 * @package MW
 * @subpackage Session
 */
class MW_Session_PHP implements MW_Session_Iface
{
	public function __construct()
	{
		session_start();
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
	public function get( $name, $default = null )
	{
		if( isset( $SESSION[$name] ) ) {
			return $SESSION[$name];
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
	 * @return void
	 */
	public function set( $name, $value )
	{
		$SESSION[$name] = $value;
	}
}

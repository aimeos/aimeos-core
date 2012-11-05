<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id: APC.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * APC caching decorator for config classes.
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Decorator_APC implements MW_Config_Decorator_Interface
{
	private $_object;
	private $_prefix;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Config_Interface $object Config object or decorator
	 */
	public function __construct( MW_Config_Interface $object )
	{
		if( function_exists( 'apc_store' ) === false ) {
			throw new MW_Config_Exception( 'APC not available' );
		}

		$this->_object = $object;
		$this->_prefix = $object->get( 'resource/config/decorator/prefix', 'config:' );
	}


	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $path Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( $path, $default = null )
	{
		$path = trim( $path, '/' );

		// negative cache
		$success = false;
		$value = apc_fetch( '-' . $this->_prefix . $path, $success );

		if( $success === true ) {
			return $default;
		}

		// regular cache
		$success = false;
		$value = apc_fetch( $this->_prefix . $path, $success );

		if( $success === true ) {
			return $value;
		}

		// not cached
		if( ( $value = $this->_object->get( $path, null ) ) === null )
		{
			apc_store( '-' . $this->_prefix . $path, null );
			return $default;
		}

		apc_store( $this->_prefix . $path, $value );

		return $value;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $path Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 */
	public function set( $path, $value )
	{
		$path = trim( $path, '/' );

		$this->_object->set( $path, $value );

		apc_store( $this->_prefix . $path, $value );
	}
}

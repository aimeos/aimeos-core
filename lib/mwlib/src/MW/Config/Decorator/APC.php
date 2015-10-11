<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config\Decorator;


/**
 * APC caching decorator for config classes.
 *
 * @package MW
 * @subpackage Config
 */
class APC
	extends \Aimeos\MW\Config\Decorator\Base
	implements \Aimeos\MW\Config\Decorator\Iface
{
	private $prefix;


	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Config\Iface $object Config object or decorator
	 * @param string $prefix Prefix for keys to distinguish several instances
	 */
	public function __construct( \Aimeos\MW\Config\Iface $object, $prefix = '' )
	{
		if( function_exists( 'apc_store' ) === false ) {
			throw new \Aimeos\MW\Config\Exception( 'APC not available' );
		}

		parent::__construct( $object );
		$this->prefix = $prefix;
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
		apc_fetch( '-' . $this->prefix . $path, $success );

		if( $success === true ) {
			return $default;
		}

		// regular cache
		$success = false;
		$value = apc_fetch( $this->prefix . $path, $success );

		if( $success === true ) {
			return $value;
		}

		// not cached
		if( ( $value = $this->getObject()->get( $path, null ) ) === null )
		{
			apc_store( '-' . $this->prefix . $path, null );
			return $default;
		}

		apc_store( $this->prefix . $path, $value );

		return $value;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $path Path to the requested value like tree/node/classname
	 * @param string $value Value that should be associated with the given path
	 */
	public function set( $path, $value )
	{
		$path = trim( $path, '/' );

		$this->getObject()->set( $path, $value );

		apc_store( $this->prefix . $path, $value );
	}
}

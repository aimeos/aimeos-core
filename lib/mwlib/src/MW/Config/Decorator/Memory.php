<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config\Decorator;


/**
 * Memory caching decorator for config classes.
 *
 * @package MW
 * @subpackage Config
 */
class Memory
	extends \Aimeos\MW\Config\Decorator\Base
	implements \Aimeos\MW\Config\Decorator\Iface
{
	private $negCache = [];
	private $cache = [];
	private $config;


	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Config\Iface $object Config object or decorator
	 * @param array $config Pre-cached non-shared configuration
	 */
	public function __construct( \Aimeos\MW\Config\Iface $object, array $config = [] )
	{
		parent::__construct( $object );

		$this->config = $config;
	}


	/**
	 * Adds the given configuration and overwrite already existing keys.
	 *
	 * @param array $config Associative list of (multi-dimensional) configuration settings
	 * @return \Aimeos\MW\Config\Iface Config instance for method chaining
	 */
	public function apply( array $config ) : \Aimeos\MW\Config\Iface
	{
		$this->config = array_replace_recursive( $this->config, $config );
		return $this;
	}


	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( string $name, $default = null )
	{
		$name = trim( $name, '/' );

		if( isset( $this->negCache[$name] ) ) {
			return $default;
		}

		if( array_key_exists( $name, $this->cache ) ) {
			return $this->cache[$name];
		}

		if( ( $value = $this->getValueFromArray( $this->config, explode( '/', $name ) ) ) === null ) {
			$value = parent::get( $name, null );
		}

		if( $value === null )
		{
			$this->negCache[$name] = true;
			return $default;
		}

		$this->cache[$name] = $value;
		return $value;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param string $value Value that should be associated with the given path
	 * @return \Aimeos\MW\Config\Iface Config instance for method chaining
	 */
	public function set( string $name, $value ) : \Aimeos\MW\Config\Iface
	{
		$name = trim( $name, '/' );

		if( $value !== null )
		{
			$this->cache[$name] = $value;
			unset( $this->negCache[$name] );
		}
		else
		{
			$this->negCache[$name] = true;
		}

		// don't store local configuration
		return $this;
	}


	/**
	 * Returns the requested configuration value from the given array
	 *
	 * @param array $config The array to search in
	 * @param array $parts Configuration path parts to look for inside the array
	 * @return mixed Found configuration value or null if not available
	 */
	protected function getValueFromArray( array $config, array $parts )
	{
		if( ( $key = array_shift( $parts ) ) !== null && isset( $config[$key] ) )
		{
			if( count( $parts ) > 0 ) {
				return $this->getValueFromArray( $config[$key], $parts );
			}

			return $config[$key];
		}

		return null;
	}
}

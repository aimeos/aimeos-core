<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
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
	private $negCache = array();
	private $cache = array();
	private $config;


	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Config\Iface $object Config object or decorator
	 * @param array $config Pre-cached non-shared configuration
	 */
	public function __construct( \Aimeos\MW\Config\Iface $object, $config = array() )
	{
		parent::__construct( $object );

		$this->config = $config;
	}


	/**
	 * Returns the value of the requested config key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( $name, $default = null )
	{
		$name = trim( $name, '/' );

		if( isset( $this->negCache[ $name ] ) ) {
			return $default;
		}

		if( array_key_exists( $name, $this->cache ) ) {
			return $this->cache[ $name ];
		}

		if( ( $return = $this->getValueFromArray( $this->config, explode( '/', $name ) ) ) !== null ) {
			return $return;
		}

		$return = $this->getObject()->get( $name, null );

		if( $return === null )
		{
			$this->negCache[ $name ] = true;
			return $default;
		}

		$this->cache[ $name ] = $return;
		return $return;
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param string $value Value that should be associated with the given path
	 */
	public function set( $name, $value )
	{
		$name = trim( $name, '/' );

		if( $value !== null )
		{
			$this->cache[ $name ] = $value;

			if( isset( $this->negCache[ $name ] ) ) {
				unset( $this->negCache[ $name ] );
			}
		}
		else
		{
			$this->negCache[ $name ] = true;
		}

		// don't store local configuration
	}


	/**
	 * Returns the requested configuration value from the given array
	 *
	 * @param array $config The array to search in
	 * @param array $parts Configuration path parts to look for inside the array
	 * @return mixed Found configuration value or null if not available
	 */
	protected function getValueFromArray( $config, $parts )
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
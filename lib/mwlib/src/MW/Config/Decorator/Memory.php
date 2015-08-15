<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 */


/**
 * Memory caching decorator for config classes.
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Decorator_Memory
	extends MW_Config_Decorator_Abstract
	implements MW_Config_Decorator_Interface
{
	private $_negCache = array();
	private $_cache = array();
	private $_config;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Config_Interface $object Config object or decorator
	 * @param array $config Pre-cached non-shared configuration
	 */
	public function __construct( MW_Config_Interface $object, $config = array() )
	{
		parent::__construct( $object );

		$this->_config = $config;
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

		if( isset( $this->_negCache[ $name ] ) ) {
			return $default;
		}

		if( array_key_exists( $name, $this->_cache ) ) {
			return $this->_cache[ $name ];
		}

		if( ( $return = $this->_getValueFromArray( $this->_config, explode( '/', $name ) ) ) !== null ) {
			return $return;
		}

		$return = $this->_getObject()->get( $name, null );

		if( $return === null )
		{
			$this->_negCache[ $name ] = true;
			return $default;
		}

		$this->_cache[ $name ] = $return;
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
			$this->_cache[ $name ] = $value;

			if( isset( $this->_negCache[ $name ] ) ) {
				unset( $this->_negCache[ $name ] );
			}
		}
		else
		{
			$this->_negCache[ $name ] = true;
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
	protected function _getValueFromArray( $config, $parts )
	{
		if( ( $key = array_shift( $parts ) ) !== null && isset( $config[$key] ) )
		{
			if( count( $parts ) > 0 ) {
				return $this->_getValueFromArray( $config[$key], $parts );
			}

			return $config[$key];
		}

		return null;
	}
}
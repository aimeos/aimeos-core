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
class MW_Config_Decorator_MemoryCache
	extends MW_Config_Decorator_Abstract
	implements MW_Config_Decorator_Interface
{
	protected $_cache = array();
	protected $_negCache = array();


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
	 * @param mixed $value Value that should be associated with the given path
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

		$this->_getObject()->set( $name, $value );
	}

}
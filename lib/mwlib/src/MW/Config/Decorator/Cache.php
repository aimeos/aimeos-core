<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 * @version $Id$
 */


/**
 * Caching decorator for config classes.
 *
 * @package MW
 * @subpackage Config
 */
class MW_Config_Decorator_Cache implements MW_Config_Decorator_Interface
{
	
	protected $_object = null;
	protected $_cache = array();
	protected $_negCache = array();
	protected $_prefix;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Config_Interface $object Config object or decorator
	 */
	public function __construct( MW_Config_Interface $object )
	{
		$this->_object = $object;
		$this->_prefix = $object->get( 'resource/config/decorator/prefix', 'config:' );
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

		if( isset( $this->_negCache[ $name ] ) && ( $this->_negCache[ $name ] === true ) ) {
			return $default;
		}

		if( isset( $this->_cache[ $name ] ) ) {
			return $this->_cache[ $name ];
		}

		$return = $this->_object->get( $name, $default );

		if( $return === null || $return === array() )
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
		if( $value === null ) {
			$this->_negCache[ $name ] = true;
		} else {
			$name = trim( $name, '/' );
			$this->_cache[ $name ] = $value;

			if( isset( $this->_negCache[ $name ] ) ) {
				unset( $this->_negCache[ $name ] );
			}
		}
	}

}
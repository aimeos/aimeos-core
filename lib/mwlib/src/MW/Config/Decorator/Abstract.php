<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Config
 */


/**
 * Base class for all config decorators.
 *
 * @package MW
 * @subpackage Config
 */
abstract class MW_Config_Decorator_Abstract implements MW_Config_Decorator_Interface
{
	private $object;


	/**
	 * Initializes the decorator.
	 *
	 * @param MW_Config_Interface $object Config object or decorator
	 */
	public function __construct( MW_Config_Interface $object )
	{
		$this->object = $object;
	}


	/**
	 * Clones the objects inside.
	 */
	public function __clone()
	{
		$this->object = clone $this->object;
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
		$this->object->get( $path, $default );
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $path Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 */
	public function set( $path, $value )
	{
		$this->object->set( $path, $value );
	}


	/**
	 * Returns the wrapped config object.
	 *
	 * @return MW_Config_Interface Config object
	 */
	protected function getObject()
	{
		return $this->object;
	}
}

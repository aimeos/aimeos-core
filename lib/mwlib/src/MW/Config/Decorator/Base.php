<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Config
 */


namespace Aimeos\MW\Config\Decorator;


/**
 * Base class for all config decorators.
 *
 * @package MW
 * @subpackage Config
 */
abstract class Base implements \Aimeos\MW\Config\Decorator\Iface
{
	private $object;


	/**
	 * Initializes the decorator.
	 *
	 * @param \Aimeos\MW\Config\Iface $object Config object or decorator
	 */
	public function __construct( \Aimeos\MW\Config\Iface $object )
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
		return $this->object->get( $path, $default );
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
	 * @return \Aimeos\MW\Config\Iface Config object
	 */
	protected function getObject()
	{
		return $this->object;
	}
}

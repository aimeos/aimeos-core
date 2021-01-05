<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container;


/**
 * Common abstract class for container objects.
 *
 * @package MW
 * @subpackage Container
 */
abstract class Base
{
	private $options;
	private $name;


	/**
	 * Initializes the object.
	 *
	 * @param string $name File path and name of the resource
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( string $name, array $options )
	{
		$this->options = $options;
		$this->name = $name;
	}


	/**
	 * Returns the path of the actual file.
	 *
	 * @return string Path to the actual file
	 */
	public function getName() : string
	{
		return $this->name;
	}


	/**
	 * Returns all options as array.
	 *
	 * @return array Associative list of option keys and values
	 */
	protected function getOptions() : array
	{
		return $this->options;
	}


	/**
	 * Returns the configured value for the given name or the default value if nothing is configured.
	 *
	 * @param string $name Name of the configuration option
	 * @param mixed $default Default value if option is not configured
	 * @return string Option value
	 */
	protected function getOption( string $name, $default = null )
	{
		return ( isset( $this->options[$name] ) ? $this->options[$name] : $default );
	}
}

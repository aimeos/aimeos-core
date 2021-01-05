<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Container
 */


namespace Aimeos\MW\Container\Content;


/**
 * Common abstract class for content objects.
 *
 * @package MW
 * @subpackage Container
 */
abstract class Base
{
	private $resource;
	private $options;
	private $name;


	/**
	 * Initializes the CSV content object.
	 *
	 * @param string $resource Path to the actual file
	 * @param string $name Name of the CSV file
	 * @param array $options Associative list of key/value pairs for configuration
	 */
	public function __construct( string $resource, string $name, array $options )
	{
		$this->resource = $resource;
		$this->options = $options;
		$this->name = $name;
	}


	/**
	 * Returns the path of the actual file.
	 *
	 * @return string Path to the actual file
	 */
	public function getResource() : string
	{
		return $this->resource;
	}


	/**
	 * Returns the name of the content object.
	 *
	 * @return string Name of the content object
	 */
	public function getName() : string
	{
		return $this->name;
	}


	/**
	 * Returns the configured value for the given name or the default value if nothing is configured.
	 *
	 * @param string $name Name of the configuration option
	 * @param mixed $default Default value if option is not configured
	 * @return mixed Option value
	 */
	protected function getOption( string $name, $default = null )
	{
		return ( isset( $this->options[$name] ) ? $this->options[$name] : $default );
	}
}

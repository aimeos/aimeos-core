<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Filesystem
 */


namespace Aimeos\MW\Filesystem\Manager;


/**
 * Standard file system manager
 *
 * @package MW
 * @subpackage Filesystem
 */
class Standard implements Iface
{
	private $config;
	private $objects = [];


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MW\Config\Iface $config Configuration object
	 */
	public function __construct( \Aimeos\MW\Config\Iface $config )
	{
		$this->config = $config;
	}


	/**
	 * Cleans up the object
	 */
	public function __destruct()
	{
		foreach( $this->objects as $key => $object ) {
			unset( $this->objects[$key] );
		}
	}


	/**
	 * Clean up the objects inside
	 */
	public function __sleep()
	{
		$this->__destruct();

		$this->objects = [];

		return get_object_vars( $this );
	}


	/**
	 * Returns the file system for the given name
	 *
	 * @param string $name Key for the file system
	 * @return \Aimeos\MW\Filesystem\Iface File system object
	 * @throws \Aimeos\MW\Filesystem\Exception If an no configuration for that name is found
	 */
	public function get( string $name ) : \Aimeos\MW\Filesystem\Iface
	{
		if( !isset( $this->objects[$name] ) ) {
			$this->objects[$name] = \Aimeos\MW\Filesystem\Factory::create( (array) $this->getConfig( $name ) );
		}

		return $this->objects[$name];
	}


	/**
	 * Returns the configuration for the given name
	 *
	 * @param string $name Name of the resource, e.g. "fs" or "fs-media"
	 * @return array|string Configuration values
	 * @throws \Aimeos\MW\Filesystem\Exception If an no configuration for that name is found
	 */
	protected function getConfig( string $name )
	{
		if( ( $conf = $this->config->get( 'resource/' . $name ) ) !== null ) {
			return $conf;
		}

		$name = 'fs';
		if( ( $conf = $this->config->get( 'resource/fs' ) ) !== null ) {
			return $conf;
		}

		$msg = sprintf( 'No resource configuration for "%1$s" available', $name );
		throw new \Aimeos\MW\Filesystem\Exception( $msg );
	}
}

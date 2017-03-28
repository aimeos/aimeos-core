<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	 * Returns the file system for the given name
	 *
	 * @param string $name Key for the file system
	 * @return \Aimeos\MW\Filesystem\Iface File system object
	 * @throws \Aimeos\MW\Filesystem\Exception If an no configuration for that name is found
	 */
	public function get( $name )
	{
		$conf = (array) $this->getConfig( $name );

		if( !isset( $this->objects[$name] ) ) {
			$this->objects[$name] = \Aimeos\MW\Filesystem\Factory::create( $conf );
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
	protected function getConfig( $name )
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

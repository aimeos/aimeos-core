<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Filesystem
 */


namespace Aimeos\MW\Filesystem\Manager;


/**
 * Interface for file system managers
 *
 * @package MW
 * @subpackage Filesystem
 */
interface Iface
{
	/**
	 * Returns the file system for the given name
	 *
	 * @param string $name Key for the file system
	 * @return \Aimeos\MW\Filesystem\Iface File system object
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function get( string $name ) : \Aimeos\MW\Filesystem\Iface;
}

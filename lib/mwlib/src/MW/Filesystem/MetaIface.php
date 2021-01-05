<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Filesystem
 */


namespace Aimeos\MW\Filesystem;


/**
 * Interface for supporting metadata
 *
 * @package MW
 * @subpackage Filesystem
 */
interface MetaIface
{
	/**
	 * Returns the file size
	 *
	 * @param string $path Path to the file
	 * @return int Size in bytes
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function size( string $path ) : int;

	/**
	 * Returns the Unix time stamp for the file
	 *
	 * @param string $path Path to the file
	 * @return int Unix time stamp in seconds
	 * @throws \Aimeos\MW\Filesystem\Exception If an error occurs
	 */
	public function time( string $path ) : int;
}

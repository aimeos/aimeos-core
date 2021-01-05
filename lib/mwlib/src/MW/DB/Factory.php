<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage DB
 */


namespace Aimeos\MW\DB;


/**
 * Creates new database manager instances.
 *
 * @package MW
 * @subpackage DB
 */
class Factory
{
	/**
	 * Creates and returns a database manager.
	 *
	 * @param \Aimeos\MW\Config\Iface $config Configuration class instance
	 * @param string $type Name of the manager
	 * @return \Aimeos\MW\DB\Manager\Iface Instance of a database manager
	 * @throws \Aimeos\MW\DB\Exception if database manager class isn't found
	 */
	public static function create( \Aimeos\MW\Config\Iface $config, $type = 'PDO' )
	{
		$classname = '\Aimeos\MW\DB\Manager\\' . $type;
		$filename = 'MW/DB/Manager/' . $type . '.php';

		$paths = explode( PATH_SEPARATOR, get_include_path() );

		foreach( $paths as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $filename;
			if( file_exists( $file ) === true && ( include_once $file ) !== false && class_exists( $classname ) ) {
				return new $classname( $config );
			}
		}

		throw new \Aimeos\MW\DB\Exception( sprintf( 'Database manager "%1$s" not found', $type ) );
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage DB
 * @version $Id: Factory.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Creates new database manager instances.
 *
 * @package MW
 * @subpackage DB
 */
class MW_DB_Factory
{
	/**
	 * Creates and returns a database manager.
	 *
	 * @param MW_Config_Interface $config Configuration class instance
	 * @param string $type Name of the manager
	 * @return MW_DB_Manager_Interface Instance of a database manager
	 * @throws MW_DB_Exception if database manager class isn't found
	 */
	static public function createManager( MW_Config_Interface $config, $type = 'Default' )
	{
		$classname = 'MW_DB_Manager_' . $type;
		$filename = str_replace( '_', '/', $classname ) . '.php';
		
		$paths = explode( PATH_SEPARATOR, get_include_path() );

		foreach( $paths as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $filename;
			if( file_exists( $file ) === true  && ( include_once $file ) !== false && class_exists($classname)) {
				return new $classname( $config );
			}
		}

		throw new MW_DB_Exception( sprintf( 'Database manager "%1$s" not found', $type ) );
	}
}

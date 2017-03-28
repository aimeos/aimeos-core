<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


class TestHelperMw
{
	private static $config;
	private static $dbm;


	/**
	 * Autoloader for classes
	 *
	 * @param string $className Class name
	 * @return boolean True if class was found, false if not
	 */
	public static function autoload( $className )
	{
		$fileName = strtr( ltrim( $className, '\\' ), '\\_', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR ) . '.php';

		if( strncmp( $fileName, 'Aimeos' . DIRECTORY_SEPARATOR, 7 ) === 0 ) {
			$fileName = substr( $fileName, 7 );
		}

		foreach( explode( PATH_SEPARATOR, get_include_path() ) as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $fileName;

			if( file_exists( $file ) === true && ( include_once $file ) !== false ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Returns the configuration object
	 *
	 * @return \Aimeos\MW\Config\Iface Configuration object
	 */
	public static function getConfig()
	{
		if( !isset( self::$config ) ) {
			self::$config = self::createConfig();
		}

		return self::$config;
	}


	/**
	 * Returns the database manager object
	 *
	 * @return \Aimeos\MW\DB\Manager\Iface Database manager object
	 */
	public static function getDBManager()
	{
		if( !isset( self::$dbm ) ) {
			self::$dbm = self::createDBManager();
		}

		return self::$dbm;
	}


	/**
	 * Creates a new configuration object
	 *
	 * @return \Aimeos\MW\Config\Iface Configuration object
	 */
	private static function createConfig()
	{
		$path = dirname( dirname( dirname( __DIR__ ) ) ) . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';

		$object = new \Aimeos\MW\Config\PHPArray( [], $path );
		$object = new \Aimeos\MW\Config\Decorator\Documentor( $object, $file );

		return $object;
	}


	/**
	 * Creates a new database manager object
	 *
	 * @return \Aimeos\MW\DB\Manager\Iface Database manager object
	 */
	private static function createDBManager()
	{
		return \Aimeos\MW\DB\Factory::createManager( self::getConfig(), 'DBAL' );
	}
}

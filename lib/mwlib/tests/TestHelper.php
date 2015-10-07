<?php


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class TestHelper
{
	private static $config;
	private static $dbm;


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


	public static function getConfig()
	{
		if( !isset( self::$config ) ) {
			self::$config = self::createConfig();
		}

		return self::$config;
	}


	public static function getDBManager()
	{
		if( !isset( self::$dbm ) ) {
			self::$dbm = self::createDBManager();
		}

		return self::$dbm;
	}


	private static function createConfig()
	{
		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';

		$object = new \Aimeos\MW\Config\PHPArray( array(), $path );
		$object = new \Aimeos\MW\Config\Decorator\Documentor( $object, $file );

		return $object;
	}


	private static function createDBManager()
	{
		return \Aimeos\MW\DB\Factory::createManager( self::getConfig(), 'PDO' );
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


class TestHelper
{
	private static $config;
	private static $dbm;


	public static function autoload( $classname )
	{
		$filename = str_replace( '_', '/', $classname ) . '.php';
		$paths = explode( PATH_SEPARATOR, get_include_path() );

		foreach( $paths as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $filename;
			if( file_exists( $file ) === true  && ( include_once $file ) !== false ) {
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

		$object = new MW_Config_Array( array(), $path );
		$object = new MW_Config_Decorator_Documentor( $object, $file );

		return $object;
	}


	private static function createDBManager()
	{
		return MW_DB_Factory::createManager( self::getConfig(), 'PDO' );
	}
}

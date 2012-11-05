<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id: TestHelper.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


class MW_TestHelper
{
	private static $_config;
	private static $_dbm;


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
		if( !isset( self::$_config ) ) {
			self::$_config = self::_createConfig();
		}

		return self::$_config;
	}


	public static function getDBManager()
	{
		if( !isset( self::$_dbm ) ) {
			self::$_dbm = self::_createDBManager();
		}

		return self::$_dbm;
	}


	private static function _createConfig()
	{
		$path = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'config';
		return new MW_Config_Zend( new Zend_Config( array(), true ), $path );
	}


	private static function _createDBManager()
	{
		return MW_DB_Factory::createManager( self::getConfig(), 'PDO' );
	}
}

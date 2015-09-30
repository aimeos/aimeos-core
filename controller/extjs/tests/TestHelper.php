<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class TestHelper
{
	private static $_aimeos;
	private static $_context;


	public static function bootstrap()
	{
		set_error_handler( 'TestHelper::errorHandler' );

		self::_getAimeos();
		MShop_Factory::setCache( false );
		Controller_ExtJS_Factory::setCache( false );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$_context[$site] ) ) {
			self::$_context[$site] = self::_createContext( $site );
		}

		return clone self::$_context[$site];
	}


	private static function _getAimeos()
	{
		if( !isset( self::$_aimeos ) )
		{
			require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR . 'Aimeos.php';

			self::$_aimeos = new Aimeos();
		}

		return self::$_aimeos;
	}


	public static function getControllerPaths()
	{
		return self::_getAimeos()->getCustomPaths( 'controller/extjs' );
	}


	/**
	 * @param string $site
	 */
	private static function _createContext( $site )
	{
		$ctx = new MShop_Context_Item_Default();
		$aimeos = self::_getAimeos();


		$paths = $aimeos->getConfigPaths( 'mysql' );
		$paths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';

		$conf = new MW_Config_Array( array(), $paths );
		$conf = new MW_Config_Decorator_Memory( $conf );
		$conf = new MW_Config_Decorator_Documentor( $conf, $file );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Abstract::DEBUG );
		$ctx->setLogger( $logger );


		$cache = new MW_Cache_None();
		$ctx->setCache( $cache );


		$session = new MW_Session_None();
		$ctx->setSession( $session );

		$i18n = new MW_Translation_None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );

		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		$ctx->setEditor( 'core:controller/extjs' );

		return $ctx;
	}


	public static function errorHandler( $code, $message, $file, $row )
	{
		return true;
	}

}

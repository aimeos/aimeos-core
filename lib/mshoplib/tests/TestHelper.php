<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: TestHelper.php 14830 2012-01-12 16:58:09Z fblasel $
 */


class TestHelper
{
	private static $_mshop;
	private static $_context = array();


	public static function bootstrap()
	{
		$mshop = self::_getMShop();

		$includepaths = $mshop->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$_context[$site] ) ) {
			self::$_context[$site] = self::_createContext( $site );
		}

		return self::$_context[$site];
	}


	private static function _getMShop()
	{
		if( !isset( self::$_mshop ) )
		{
			require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR . 'MShop.php';
			spl_autoload_register( 'MShop::autoload' );

			self::$_mshop = new MShop( array(), false );
		}

		return self::$_mshop;
	}


	private static function _createContext( $site )
	{
		$ctx = new MShop_Context_Item_Default();
		$mshop = self::_getMShop();


		$paths = $mshop->getConfigPaths( 'mysql' );
		$paths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_Array( array(), $paths );
		$conf = new MW_Config_Decorator_MemoryCache( $conf );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Abstract::DEBUG );
		$ctx->setLogger( $logger );


		$cache = new MW_Cache_None();
		$ctx->setCache( $cache );


		$i18n = new MW_Translation_None( 'en' );
		$ctx->setI18n( $i18n );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		$ctx->setEditor( 'core:unittest' );

		return $ctx;
	}
}

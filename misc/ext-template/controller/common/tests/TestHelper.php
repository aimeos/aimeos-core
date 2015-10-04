<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class TestHelper
{
	private static $aimeos;
	private static $context;


	public static function bootstrap()
	{
		$aimeos = self::getAimeos();

		$includepaths = $aimeos->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );

		spl_autoload_register( 'Aimeos::autoload' );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	private static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once 'Aimeos.php';
			spl_autoload_register( 'Aimeos::autoload' );

			$extdir = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
			self::$aimeos = new Aimeos( array( $extdir ), false );
		}

		return self::$aimeos;
	}


	private static function createContext( $site )
	{
		$ctx = new MShop_Context_Item_Standard();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths( 'mysql' );
		$paths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_PHPArray( array(), $paths );
		$conf = new MW_Config_Decorator_Memory( $conf );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Base::DEBUG );
		$ctx->setLogger( $logger );


		$cache = new MW_Cache_None();
		$ctx->setCache( $cache );


		$i18n = new MW_Translation_None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		$ctx->setEditor( '<extname>:unittest' );

		return $ctx;
	}
}

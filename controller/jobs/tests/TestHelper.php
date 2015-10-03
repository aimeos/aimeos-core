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
		self::getAimeos();
		MShop_Factory::setCache( false );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	public static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR . 'Aimeos.php';

			self::$aimeos = new Aimeos( array(), false );
		}

		return self::$aimeos;
	}


	public static function getControllerPaths()
	{
		return self::getAimeos()->getCustomPaths( 'controller/jobs' );
	}


	/**
	 * @param string $site
	 */
	private static function createContext( $site )
	{
		$ctx = new MShop_Context_Item_Default();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths( 'mysql' );
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';

		$conf = new MW_Config_Array( array(), $paths );
		$conf = new MW_Config_Decorator_Memory( $conf );
		$conf = new MW_Config_Decorator_Documentor( $conf, $file );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Base::DEBUG );
		$ctx->setLogger( $logger );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$i18n = new MW_Translation_None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, 'de', '', false );
		$ctx->setLocale( $locale );


		$view = self::createView( $conf );
		$ctx->setView( $view );


		$ctx->setEditor( 'core:controller/jobs' );

		return $ctx;
	}


	protected static function createView( MW_Config_Iface $config )
	{
		$tmplpaths = self::getAimeos()->getCustomPaths( 'client/html' );
		$tmplpaths[dirname( __DIR__ )] = array( 'layouts' );

		$view = new MW_View_Default();

		$trans = new MW_Translation_None( 'de_DE' );
		$helper = new MW_View_Helper_Translate_Default( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new MW_View_Helper_Url_Default( $view, 'http://baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Number_Default( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_Date_Default( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, array() );
		$view->addHelper( 'param', $helper );

		$helper = new MW_View_Helper_FormParam_Default( $view );
		$view->addHelper( 'formparam', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		$helper = new MW_View_Helper_Partial_Default( $view, $config, $tmplpaths );
		$view->addHelper( 'partial', $helper );

		return $view;
	}


	public static function errorHandler( $code, $message, $file, $row )
	{
		return true;
	}

}

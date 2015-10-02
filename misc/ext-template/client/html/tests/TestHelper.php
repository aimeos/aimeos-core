<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class TestHelper
{
	private static $_aimeos;
	private static $_context = array();


	public static function bootstrap()
	{
		$aimeos = self::_getAimeos();

		$includepaths = $aimeos->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$_context[$site] ) ) {
			self::$_context[$site] = self::_createContext( $site );
		}

		return clone self::$_context[$site];
	}


	public static function getView()
	{
		$view = new MW_View_Default();

		$trans = new MW_Translation_None( 'en' );
		$helper = new MW_View_Helper_Translate_Default( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new MW_View_Helper_Url_Default( $view, 'baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Number_Default( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_Date_Default( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, self::getContext()->getConfig() );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, array() );
		$view->addHelper( 'param', $helper );

		$helper = new MW_View_Helper_FormParam_Default( $view );
		$view->addHelper( 'formparam', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		return $view;
	}


	public static function getHtmlTemplatePaths()
	{
		return self::_getAimeos()->getCustomPaths( 'client/html' );
	}


	private static function _getAimeos()
	{
		if( !isset( self::$_aimeos ) )
		{
			require_once 'Aimeos.php';
			spl_autoload_register( 'Aimeos::autoload' );

			$extdir = dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) );
			self::$_aimeos = new Aimeos( array( $extdir ), false );
		}

		return self::$_aimeos;
	}


	private static function _createContext( $site )
	{
		$ctx = new MShop_Context_Item_Default();
		$aimeos = self::_getAimeos();


		$paths = $aimeos->getConfigPaths( 'mysql' );
		$paths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_Array( array(), $paths );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Abstract::DEBUG );
		$ctx->setLogger( $logger );


		$cache = new MW_Cache_None();
		$ctx->setCache( $cache );


		$i18n = new MW_Translation_None( 'en' );
		$ctx->setI18n( array( 'en' => $i18n ) );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		$ctx->setEditor( '<extname>:unittest' );

		return $ctx;
	}
}
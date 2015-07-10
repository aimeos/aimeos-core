<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class TestHelper
{
	private static $_arcavias;
	private static $_context;


	public static function bootstrap()
	{
		self::getArcavias();
		MShop_Factory::setCache( false );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$_context[$site] ) ) {
			self::$_context[$site] = self::_createContext( $site );
		}

		return clone self::$_context[$site];
	}


	public static function getArcavias()
	{
		if( !isset( self::$_arcavias ) )
		{
			require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR . 'Arcavias.php';

			self::$_arcavias = new Arcavias( array(), false );
		}

		return self::$_arcavias;
	}


	public static function getControllerPaths()
	{
		return self::getArcavias()->getCustomPaths( 'controller/jobs' );
	}


	/**
	 * @param string $site
	 */
	private static function _createContext( $site )
	{
		$ctx = new MShop_Context_Item_Default();
		$arcavias = self::getArcavias();


		$paths = $arcavias->getConfigPaths( 'mysql' );
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';

		$conf = new MW_Config_Array( array(), $paths );
		$conf = new MW_Config_Decorator_Memory( $conf );
		$conf = new MW_Config_Decorator_Documentor( $conf, $file );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Abstract::DEBUG );
		$ctx->setLogger( $logger );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$i18n = new MW_Translation_None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, 'de', '', false );
		$ctx->setLocale( $locale );


		$view = self::_createView( $conf );
		$ctx->setView( $view );


		$ctx->setEditor( 'core:controller/jobs' );

		return $ctx;
	}


	protected static function _createView( MW_Config_Interface $config )
	{
		$tmplpaths = self::getArcavias()->getCustomPaths( 'client/html' );
		$tmplpaths[ dirname(__DIR__) ] = array( 'layouts' );

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


	public static function errorHandler($code, $message, $file, $row)
	{
		return true;
	}

}

<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class TestHelperHtml
{
	private static $aimeos;
	private static $context = array();


	public static function bootstrap()
	{
		self::getAimeos();
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\Controller\Frontend\Factory::setCache( false );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	public static function getView( $site = 'unittest', \Aimeos\MW\Config\Iface $config = null )
	{
		if( $config === null ) {
			$config = self::getContext( $site )->getConfig();
		}

		$view = new \Aimeos\MW\View\Standard( self::getHtmlTemplatePaths() );

		$trans = new \Aimeos\MW\Translation\None( 'de_DE' );
		$helper = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\MW\View\Helper\Url\Standard( $view, 'http://baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\MW\View\Helper\Number\Standard( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\MW\View\Helper\Date\Standard( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $view, 'body', '127.0.0.1' );
		$view->addHelper( 'request', $helper );

		$helper = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, '_csrf_token', '_csrf_value' );
		$view->addHelper( 'csrf', $helper );

		return $view;
	}


	public static function getHtmlTemplatePaths()
	{
		return self::getAimeos()->getCustomPaths( 'client/html/templates' );
	}


	private static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once dirname( dirname( dirname( __DIR__ ) ) ) . DIRECTORY_SEPARATOR . 'Bootstrap.php';

			self::$aimeos = new \Aimeos\Bootstrap( array(), false );
		}

		return self::$aimeos;
	}


	/**
	 * @param string $site
	 */
	private static function createContext( $site )
	{
		$ctx = new \Aimeos\MShop\Context\Item\Standard();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths( 'mysql' );
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';
		$local = array( 'resource' => array( 'fs' => array( 'adapter' => 'Standard', 'basedir' => __DIR__ . '/tmp' ) ) );

		$conf = new \Aimeos\MW\Config\PHPArray( $local, $paths );
		$conf = new \Aimeos\MW\Config\Decorator\Memory( $conf );
		$conf = new \Aimeos\MW\Config\Decorator\Documentor( $conf, $file );
		$ctx->setConfig( $conf );


		$dbm = new \Aimeos\MW\DB\Manager\PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$fs = new \Aimeos\MW\Filesystem\Manager\Standard( $conf );
		$ctx->setFilesystemManager( $fs );


		$logger = new \Aimeos\MW\Logger\File( $site . '.log', \Aimeos\MW\Logger\Base::DEBUG );
		$ctx->setLogger( $logger );


		$cache = new \Aimeos\MW\Cache\None();
		$ctx->setCache( $cache );


		$i18n = new \Aimeos\MW\Translation\None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$session = new \Aimeos\MW\Session\None();
		$ctx->setSession( $session );


		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $ctx );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		$ctx->setEditor( 'core:client/html' );

		return $ctx;
	}
}

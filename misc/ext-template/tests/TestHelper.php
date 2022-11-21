<?php


class TestHelper
{
	private static $aimeos;
	private static $context = array();


	public static function bootstrap()
	{
		$aimeos = self::getAimeos();

		$includepaths = $aimeos->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	private static function getAimeos()
	{
		if( !isset( self::$aimeos ) )
		{
			require_once 'Bootstrap.php';
			spl_autoload_register( 'Aimeos\\Bootstrap::autoload' );

			self::$aimeos = new \Aimeos\Bootstrap();
		}

		return self::$aimeos;
	}


	public static function context( $site = 'unittest' )
	{
		if( !isset( self::$context[$site] ) ) {
			self::$context[$site] = self::createContext( $site );
		}

		return clone self::$context[$site];
	}


	public static function getHtmlTemplatePaths()
	{
		return self::getAimeos()->getTemplatePaths( 'client/html/templates' );
	}


	public static function view( $site = 'unittest', \Aimeos\Base\Config\Iface $config = null )
	{
		if( $config === null ) {
			$config = self::context( $site )->config();
		}

		$view = new \Aimeos\Base\View\Standard( self::getHtmlTemplatePaths() );

		$trans = new \Aimeos\Base\Translation\None( 'de_DE' );
		$helper = new \Aimeos\Base\View\Helper\Translate\Standard( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new \Aimeos\Base\View\Helper\Url\Standard( $view, 'http://baseurl' );
		$view->addHelper( 'url', $helper );

		$helper = new \Aimeos\Base\View\Helper\Session\Standard( $view, self::context( $site )->session() );
		$view->addHelper( 'session', $helper );

		$helper = new \Aimeos\Base\View\Helper\Number\Standard( $view, '.', '' );
		$view->addHelper( 'number', $helper );

		$helper = new \Aimeos\Base\View\Helper\Date\Standard( $view, 'Y-m-d' );
		$view->addHelper( 'date', $helper );

		$paths = ['version', 'client', 'common', 'resource/fs/baseurl', 'resource/fs-media/baseurl', 'resource/fs-theme/baseurl'];
		$config = new \Aimeos\Base\Config\Decorator\Protect( $config, $paths );
		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\Base\View\Helper\Csrf\Standard( $view, '_csrf_token', '_csrf_value' );
		$view->addHelper( 'csrf', $helper );

		$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
		$helper = new \Aimeos\Base\View\Helper\Request\Standard( $view, $psr17Factory->createServerRequest( 'POST', 'https://aimeos.org' ) );
		$view->addHelper( 'request', $helper );

		$helper = new \Aimeos\Base\View\Helper\Response\Standard( $view, $psr17Factory->createResponse() );
		$view->addHelper( 'response', $helper );

		return $view;
	}


	/**
	 * @param string $site
	 */
	private static function createContext( $site )
	{
		$ctx = new \Aimeos\MShop\Context();
		$aimeos = self::getAimeos();


		$paths = $aimeos->getConfigPaths();
		$paths[] = __DIR__ . DIRECTORY_SEPARATOR . 'config';
		$file = __DIR__ . DIRECTORY_SEPARATOR . 'confdoc.ser';
		$local = array( 'resource' => array( 'fs' => array( 'adapter' => 'Standard', 'basedir' => __DIR__ . '/tmp' ) ) );

		$conf = new \Aimeos\Base\Config\PHPArray( $local, $paths );
		$conf = new \Aimeos\Base\Config\Decorator\Memory( $conf );
		$conf = new \Aimeos\Base\Config\Decorator\Documentor( $conf, $file );
		$ctx->setConfig( $conf );


		$logger = new \Aimeos\Base\Logger\File( $site . '.log', \Aimeos\Base\Logger\Iface::DEBUG );
		$ctx->setLogger( $logger );


		$dbm = new \Aimeos\Base\DB\Manager\Standard( $conf->get( 'resource', [] ), 'PDO' );
		$ctx->setDatabaseManager( $dbm );


		$fs = new \Aimeos\Base\Filesystem\Manager\Standard( $conf->get( 'resource', [] ) );
		$ctx->setFilesystemManager( $fs );


		$mq = new \Aimeos\Base\MQueue\Manager\Standard( $conf->get( 'resource', [] ) );
		$ctx->setMessageQueueManager( $mq );


		$cache = new \Aimeos\Base\Cache\None();
		$ctx->setCache( $cache );


		$i18n = new \Aimeos\Base\Translation\None( 'de' );
		$ctx->setI18n( array( 'de' => $i18n ) );


		$session = new \Aimeos\Base\Session\None();
		$ctx->setSession( $session );


		$localeManager = \Aimeos\MShop::create( $ctx, 'locale' );
		$locale = $localeManager->bootstrap( $site, '', '', false );
		$ctx->setLocale( $locale );


		return $ctx->setEditor( '<extname>' );
	}
}

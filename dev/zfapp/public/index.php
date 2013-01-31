<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: index.php 1320 2012-10-19 19:57:38Z nsendetzky $
 */

$time = microtime( true );

define( 'ZFAPP_ROOT', dirname( dirname( __FILE__ ) ) );
define( 'APPLICATION_PATH', ZFAPP_ROOT . DIRECTORY_SEPARATOR . 'application' );
define( 'APPLICATION_ENV', 'development' ); // development | production

if ( APPLICATION_ENV == 'development' ) {
	error_reporting( -1 );
	ini_set( 'display_errors', true );
}

try
{
	require_once ZFAPP_ROOT . '/../../MShop.php';
	spl_autoload_register( 'MShop::autoload' );
	$mshop = new MShop();

	$includePaths = $mshop->getIncludePaths();
	$includePaths[] = ZFAPP_ROOT . DIRECTORY_SEPARATOR . 'library';
	$includePaths[] = get_include_path();
	set_include_path( implode( PATH_SEPARATOR, $includePaths ) );

	$classFileIncCache = ZFAPP_ROOT . '/data/cache/pluginLoaderCache.php';
	include_once $classFileIncCache;

	$application = new Application_Application(
		APPLICATION_ENV,
		include_once realpath( APPLICATION_PATH . '/configs/application.php' )
	);

	$application->bootstrap()->run();

} catch ( Zend_Controller_Exception $e ) {
	include 'errors/404.phtml';
} catch ( Exception $e ) {
	include 'errors/500.phtml';
}

echo '<!-- Exec time: ' . ( ( microtime(true) - $time ) * 1000 ) . ' ms -->' . PHP_EOL;
echo '<!-- Peak mem: ' . ( memory_get_peak_usage( true ) / 1024 / 1024 ) . ' MiB -->' . PHP_EOL;
echo '<!-- Path cache: ' . ( realpath_cache_size() / 1024 ) . ' KiB -->' . PHP_EOL;

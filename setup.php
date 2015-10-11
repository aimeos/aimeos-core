<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


if( php_sapi_name() != 'cli' ) {
	exit( 'Setup can only be started via command line for security reasons' );
}

ini_set( 'display_errors', 1 );
date_default_timezone_set( 'UTC' );


function setup_autoload( $classname )
{
	if( strncmp( $classname, '\\Aimeos\\MW\\Setup\\Task\\', 14 ) === 0 )
	{
		$fileName = substr( $classname, 14 ) . '.php';
		$paths = explode( PATH_SEPARATOR, get_include_path() );

		foreach( $paths as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $fileName;

			if( file_exists( $file ) === true && ( include_once $file ) !== false ) {
				return true;
			}
		}
	}

	return false;
}


function usage()
{
	printf( "Usage: php setup.php [--extdir=<path>]* [--config=<path>]* [--option=key:value]* [sitecode]\n" );
	exit ( 1 );
}


$exectimeStart = microtime( true );

try
{
	$params = $_SERVER['argv'];
	array_shift( $params );
	$options = array();

	foreach( $params as $key => $option )
	{
		if( $option === '--help' ) {
			usage();
		}

		if( strncmp( $option, '--', 2 ) === 0 && ( $pos = strpos( $option, '=', 2 ) ) !== false )
		{
			if( ( $name = substr( $option, 2, $pos - 2 ) ) !== false )
			{
				if( isset( $options[$name] ) )
				{
					$options[$name] = (array) $options[$name];
					$options[$name][] = substr( $option, $pos + 1 );
				}
				else
				{
					$options[$name] = substr( $option, $pos + 1 );
				}

				unset( $params[$key] );
			}
			else
			{
				printf( "Invalid option \"%1\$s\"\n", $option );
				usage();
			}
		}
	}

	$site = $parent = 'default';

	if( ( $site = array_shift( $params ) ) === null ) {
		$site = 'default';
	}

	if( ( $parent = array_shift( $params ) ) === null ) {
		$parent = $site;
	}

	spl_autoload_register( 'setup_autoload' );

	require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

	$aimeos = new \Aimeos\Bootstrap( ( isset( $options['extdir'] ) ? (array) $options['extdir'] : array() ) );


	$taskPaths = $aimeos->getSetupPaths( $parent );

	$includePaths = $taskPaths;
	$includePaths[] = get_include_path();

	if( set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) === false ) {
		throw new \Exception( 'Unable to extend include path' );
	}

	$ctx = new \Aimeos\MShop\Context\Item\Standard();

	$confPaths = $aimeos->getConfigPaths( 'mysql' );
	if( isset( $options['config'] ) ) {
		$confPaths = array_merge( $confPaths, (array) $options['config'] );
	}

	$conf = new \Aimeos\MW\Config\PHPArray( array(), $confPaths );
	$conf = new \Aimeos\MW\Config\Decorator\Memory( $conf );
	$ctx->setConfig( $conf );

	$conf->set( 'setup/site', $site );

	if( isset( $options['option'] ) )
	{
		foreach( (array) $options['option'] as $option )
		{
			$parts = explode( ':', $option );

			if( count( $parts ) !== 2 )
			{
				printf( "Invalid config option \"%1\$s\"\n", $option );
				usage();
			}

			$conf->set( $parts[0], $parts[1] );
		}
	}

	$dbconfig = $conf->get( 'resource', array() );

	foreach( $dbconfig as $rname => $dbconf )
	{
		if( strncmp( $rname, 'db', 2 ) !== 0 ) {
			unset( $dbconfig[$rname] );
		} else {
			$conf->set( "resource/$rname/limit", 2 );
		}
	}

	$dbm = new \Aimeos\MW\DB\Manager\PDO( $conf );
	$ctx->setDatabaseManager( $dbm );

	$logger = new \Aimeos\MW\Logger\Errorlog( \Aimeos\MW\Logger\Base::INFO );
	$ctx->setLogger( $logger );

	$session = new \Aimeos\MW\Session\None();
	$ctx->setSession( $session );

	$cache = new \Aimeos\MW\Cache\None();
	$ctx->setCache( $cache );

	$manager = new \Aimeos\MW\Setup\Manager\Multiple( $dbm, $dbconfig, $taskPaths, $ctx );
	$manager->run( 'mysql' );
}
catch( Throwable $t )
{
	echo "\n\nCaught PHP error while processing setup";
	echo "\n\nMessage:\n";
	echo $t->getMessage();
	echo "\n\nStack trace:\n";
	echo $t->getTraceAsString();
	echo "\n\n";
	exit( 1 );
}
catch( \Exception $e )
{
	echo "\n\nCaught exception while processing setup";
	echo "\n\nMessage:\n";
	echo $e->getMessage();
	echo "\n\nStack trace:\n";
	echo $e->getTraceAsString();
	echo "\n\n";
	exit( 1 );
}

$exectimeStop = microtime( true );

printf( "Setup process took %1\$f sec\n\n", ( $exectimeStop - $exectimeStart ) );

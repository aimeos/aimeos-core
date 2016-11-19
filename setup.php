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



/**
 * Returns the command options given by the user
 *
 * @param array &$params List of parameters
 * @return array Associative list of option name and value(s)
 */
function getOptions( array &$params )
{
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

	return $options;
}


/**
 * Returns a new configuration object
 *
 * @param array $confPaths List of configuration paths from the bootstrap object
 * @param array $options Associative list of configuration options as key/value pairs
 * @return \Aimeos\MW\Config\Iface Configuration object
 */
function getConfig( array $confPaths, array $options )
{
	$config = array();

	if( isset( $options['config'] ) )
	{
		foreach( (array) $options['config'] as $path )
		{
			if( is_file( $path ) ) {
				$config = array_replace_recursive( $config, require $path );
			} else {
				$confPaths[] = $path;
			}
		}
	}

	$conf = new \Aimeos\MW\Config\PHPArray( $config, $confPaths );
	$conf = new \Aimeos\MW\Config\Decorator\Memory( $conf );

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

			$conf->set( str_replace( '\\', '/', $parts[0] ), $parts[1] );
		}
	}

	return $conf;
}


/**
 * Returns a new context object
 *
 * @param \Aimeos\MW\Config\Iface $conf Configuration object
 * @return \Aimeos\MShop\Context\Item\Iface New context object
 */
function getContext( \Aimeos\MW\Config\Iface $conf )
{
	$ctx = new \Aimeos\MShop\Context\Item\Standard();
	$ctx->setConfig( $conf );

	$dbm = new \Aimeos\MW\DB\Manager\DBAL( $conf );
	$ctx->setDatabaseManager( $dbm );

	$logger = new \Aimeos\MW\Logger\Errorlog( \Aimeos\MW\Logger\Base::INFO );
	$ctx->setLogger( $logger );

	$session = new \Aimeos\MW\Session\None();
	$ctx->setSession( $session );

	$cache = new \Aimeos\MW\Cache\None();
	$ctx->setCache( $cache );

	return $ctx;
}


/**
 * Returns the fixed and cleaned up database configuration
 *
 * @param \Aimeos\MW\Config\Iface $conf Configuration object
 * @return array Updated database configuration
 */
function getDbConfig( \Aimeos\MW\Config\Iface $conf )
{
	$dbconfig = $conf->get( 'resource', array() );

	foreach( $dbconfig as $rname => $dbconf )
	{
		if( strncmp( $rname, 'db', 2 ) !== 0 ) {
			unset( $dbconfig[$rname] );
		} else {
			$conf->set( 'resource/' . $rname . '/limit', 2 );
		}
	}

	return $dbconfig;
}



/**
 * Prints the command usage and options, exits the program after printing
 */
function usage()
{
	printf( "Usage: php setup.php [--extdir=<path>]* [--config=<path>|<file>]* [--option=key:value]* [--action=migrate|rollback|clean] [--task=<name>] [sitecode] [tplsite]\n" );
	exit ( 1 );
}



$exectimeStart = microtime( true );

try
{
	$params = $_SERVER['argv'];
	array_shift( $params );

	$options = getOptions( $params );

	if( ( $site = array_shift( $params ) ) === null ) {
		$site = 'default';
	}

	if( ( $tplsite = array_shift( $params ) ) === null ) {
		$tplsite = 'default';
	}


	require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

	$aimeos = new \Aimeos\Bootstrap( ( isset( $options['extdir'] ) ? (array) $options['extdir'] : array() ) );
	$taskPaths = $aimeos->getSetupPaths( $tplsite );

	$conf = getConfig( $aimeos->getConfigPaths(), $options );
	$conf->set( 'setup/site', $site );
	$dbconfig = getDbConfig( $conf );

	$ctx = getContext( $conf );
	$dbm = $ctx->getDatabaseManager();

	$manager = new \Aimeos\MW\Setup\Manager\Multiple( $dbm, $dbconfig, $taskPaths, $ctx );

	$action = ( isset( $options['action'] ) ? $options['action'] : 'migrate' );
	$task = ( isset( $options['task'] ) ? $options['task'] : null );

	switch( $action )
	{
		case 'clean':
			$manager->clean( $task ); break;
		case 'migrate':
			$manager->migrate( $task ); break;
		case 'rollback':
			$manager->rollback( $task ); break;
		default:
			throw new \Exception( sprintf( 'Invalid action "%1$s"', $action ) );
	}
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

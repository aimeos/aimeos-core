<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: setup.php 14823 2012-01-12 11:54:16Z nsendetzky $
 */


if( php_sapi_name() != 'cli' ) {
	exit( 'Setup can only be started via command line for security reasons' );
}

ini_set( 'display_errors', 1 );
date_default_timezone_set('UTC');


function setup_autoload( $classname )
{
	if( strncmp( $classname, 'MW_Setup_Task_', 14 ) === 0 )
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


$exectimeStart = microtime( true );

try
{
	$params = $_SERVER['argv'];
	array_shift( $params );
	$options = array();

	foreach( $params as $key => $option )
	{
		if( strncmp( $option, '--', 2 ) === 0 && ( $pos = strpos( $option, '=', 2 ) ) !== false )
		{
			if( ( $name = substr( $option, 2, $pos-2 ) ) !== false )
			{
				if( isset( $options[$name] ) )
				{
					$options[$name] = (array) $options[$name];
					$options[$name][] = substr( $option, $pos+1 );
				}
				else
				{
					$options[$name] = substr( $option, $pos+1 );
				}

				unset( $params[$key] );
			}
			else
			{
				printf( "Invalid option \"%1\$s\"\n", $option );
				printf( "Usage: php setup.php [--config=<path>] [--extdir=<path>]* [sitecode]\n" );
				exit ( 1 );
			}
		}
	}

	$site = 'default';
	if( count( $params ) > 0 && ( $site = end( $params ) ) === false ) {
			printf( "Usage: php setup.php [--config=<path>] [--extdir=<path>]* [sitecode]\n" );
			exit( 1 );
	}


	require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'MShop.php';

	spl_autoload_register( 'setup_autoload' );
	spl_autoload_register( 'MShop::autoload' );

	$mshop = new MShop( ( isset( $options['extdir'] ) ? (array) $options['extdir'] : array() ) );


	$taskPaths = $mshop->getSetupPaths( $site );

	$includePaths = $mshop->getIncludePaths();
	$includePaths = array_merge( $includePaths, $taskPaths );
	$includePaths[] = get_include_path();

	if( set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) === false ) {
		throw new Exception( 'Unable to extend include path' );
	}

	$ctx = new MShop_Context_Item_Default();

	$confPaths = $mshop->getConfigPaths( 'mysql' );
	if( isset( $options['config'] ) ) {
		$confPaths[] = $options['config'];
	}

	$conf = new MW_Config_Array( array(), $confPaths );
	$ctx->setConfig( $conf );

	if( ( $dbconfig = $conf->get( 'resource/db' ) ) === null ) {
		throw new Exception( 'Configuration for database adapter missing' );
	}
	$conf->set( 'resource/db/limit', 2 );

	$dbm = new MW_DB_Manager_PDO( $conf );
	$ctx->setDatabaseManager( $dbm );

	$logger = new MW_Logger_Errorlog( MW_Logger_ABSTRACT::INFO );
	$ctx->setLogger( $logger );

	$session = new MW_Session_None();
	$ctx->setSession( $session );

	$manager = new MW_Setup_Manager_Default( $dbm->acquire(), $dbconfig, $taskPaths, $ctx );
	$manager->run( $dbconfig['adapter'] );
}
catch( Exception $e )
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

printf( "Setup process took %1\$f sec\n\n", ($exectimeStop - $exectimeStart) );

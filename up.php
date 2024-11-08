<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


if( php_sapi_name() != 'cli' ) {
	exit( 'Setup can only be started via command line for security reasons' );
}


set_error_handler( function( $severity, $message, $file, $line ) {
	if( $severity & E_DEPRECATED === 0 ) {
		throw new ErrorException( $message, 0, $severity, $file, $line );
	}
	error_log( $message . ' in ' . $file . ' on line ' . $line );
});

ini_set( 'display_errors', 1 );
date_default_timezone_set( 'UTC' );


/**
 * Returns the configuration based on the given arguments
 *
 * @param array $options List of key/value string separated by colon (e.g. "key:value")
 * @return array Associative list of key value pairs
 */
function config( array $options ) : array
{
	$config = [];

	foreach( $options as $option )
	{
		list( $key, $val ) = explode( ':', $option );
		$config[$key] = $val;
	}

	return $config;
}


/**
 * Returns the command options given by the user
 *
 * @param array &$params List of parameters
 * @return array Associative list of option name and value(s)
 */
function options( array &$params )
{
	$options = [];

	foreach( $params as $key => $option )
	{
		if( $option === '--help' ) {
			usage();
		}

		if( !strncmp( $option, '--', 2 ) && ( $pos = strpos( $option, '=', 2 ) ) !== false )
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
		elseif( $option[0] === '-' )
		{
			$options[$option[1]] = substr( $option, 1 );
			unset( $params[$key] );
		}
	}

	return $options;
}


/**
 * Returns the verbosity level based on the given arguments
 *
 * @param array $options Associative list of key value pairs
 * @return string Verbosity level ("v", "vv", "vvv" or empty string)
 */
function verbose( array $options ) : string
{
	return isset( $options['q'] ) ? '' : ( $options['v'] ?? 'vv' );
}


/**
 * Prints the command usage and options, exits the program after printing
 */
function usage()
{
	printf( "Usage: php up.php [OPTION]* [sitecode] [tplsite]\n" );
	printf( "  -q                       Quiet\n" );
	printf( "  -v                       Important messages\n" );
	printf( "  -vv                      Important and informational messages\n" );
	printf( "  -vvv                     Important, informational and debug messages\n" );
	printf( "  --extdir=<path>          Extension directory, use several times for multiple\n" );
	printf( "  --config=<path|file>     Configuration directory, use several times for multiple\n" );
	printf( "  --option=<key>:<value>   Additional configuration key and value separated by a colon\n" );
	exit( 1 );
}



$exectimeStart = microtime( true );

try
{
	$params = $_SERVER['argv'];
	array_shift( $params );

	$options = options( $params );

	if( ( $site = array_shift( $params ) ) === null ) {
		$site = 'default';
	}

	if( ( $tplsite = array_shift( $params ) ) === null ) {
		$tplsite = 'default';
	}

	$boostrap = new \Aimeos\Bootstrap( (array) ( $options['extdir'] ?? [] ) );
	\Aimeos\Setup::use( $boostrap, config( (array) ( $options['option'] ?? [] ) ) )
		->verbose( verbose( $options ) )
		->up( $site, $tplsite );
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

if( verbose( $options ) ) {
	printf( "Setup process took %1\$f sec\n\n", ( $exectimeStop - $exectimeStart ) );
}

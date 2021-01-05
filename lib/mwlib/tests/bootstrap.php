<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


/*
 * Set error reporting to maximum
 */
error_reporting( -1 );
ini_set( 'display_errors', '1' );

date_default_timezone_set( 'UTC' );

/*
 * Set locale settings to reasonable defaults
 */
setlocale( LC_ALL, 'en_US.UTF-8' );
setlocale( LC_NUMERIC, 'POSIX' );
setlocale( LC_CTYPE, 'en_US.UTF-8' );
setlocale( LC_TIME, 'POSIX' );


/*
 * Set include path for tests
 */

require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/vendor/autoload.php';

$testdir = __DIR__;
$srcdir = dirname( $testdir ) . DIRECTORY_SEPARATOR . 'src';
$libdir = dirname( $testdir ) . DIRECTORY_SEPARATOR . 'lib';

$path = array( $testdir, $srcdir, $libdir, get_include_path() );
set_include_path( implode( PATH_SEPARATOR, $path ) );

/*
 * Use autoload function for resolving class names
 */
require_once 'TestHelperMw.php';
if( spl_autoload_register( 'TestHelperMw::autoload' ) === false ) {
	throw new \RuntimeException( 'Unable to register autoloader' );
}

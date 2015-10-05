<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/*
 * Set error reporting to maximum
 */
error_reporting( -1 );
ini_set( 'display_errors', '1' );

date_default_timezone_set('UTC');

/*
 * Set locale settings to reasonable defaults
 */
setlocale(LC_ALL, 'en_US.UTF-8');
setlocale(LC_NUMERIC, 'POSIX');
setlocale(LC_CTYPE, 'en_US.UTF-8');
setlocale(LC_TIME, 'POSIX');


/*
 * Set include path for tests
 */
$testdir =  dirname( __FILE__ );
$srcdir =  dirname( $testdir ) . DIRECTORY_SEPARATOR . 'src';
$libdir =  dirname( $testdir ) . DIRECTORY_SEPARATOR . 'lib';

$path = array( $testdir, $srcdir, $libdir, get_include_path() );
set_include_path( implode( PATH_SEPARATOR, $path ) );


/*
 * Use autoload function for resolving class names
 */
require_once 'TestHelper.php';
if( spl_autoload_register( 'TestHelper::autoload' ) === false ) {
	throw new \Exception( 'Unable to register autoloader' );
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
*/


try
{
	date_default_timezone_set('UTC');

	require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'Arcavias.php';

	spl_autoload_register( 'MShop::autoload' );

	$mshop = new MShop();


	$includePaths = $mshop->getIncludePaths();
	$includePaths[] = get_include_path();
	set_include_path( implode( PATH_SEPARATOR, $includePaths ) );


	$configPaths = $mshop->getConfigPaths( 'mysql' );
	$configPaths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';


	require_once 'Jobs.php';
	$jobs = new Jobs( $configPaths );
	$jobs->execute();
}
catch( Exception $e )
{
	error_log( sprintf( 'Error while executing "%1$s"', __FILE__ ) );
	error_log( sprintf( 'Message: "%1$s"', $e->getMessage() ) );
	error_log( $e->getTraceAsString() );
}

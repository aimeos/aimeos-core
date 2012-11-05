<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: jsonrpc.php 14399 2011-12-16 16:03:29Z nsendetzky $
 */


require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'MShop.php';

spl_autoload_register( 'MShop::autoload' );

$mshop = new MShop();

$basepath = dirname( dirname( __FILE__ ) );
$projectPath = dirname( $basepath );

$includePaths = $mshop->getIncludePaths();
$includePaths[] = $projectPath;
$includePaths[] = get_include_path();
set_include_path( implode( PATH_SEPARATOR, $includePaths ) );


$configPaths = $mshop->getConfigPaths( 'mysql' );

$configPaths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';

require_once 'Init.php';
$init = new Init( $configPaths );

echo $init->getJsonRpcController()->process( $_REQUEST, 'php://input' );

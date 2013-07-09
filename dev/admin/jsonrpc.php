<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


require_once 'Init.php';
require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'Arcavias.php';

$arcavias = new Arcavias();
$init = new Init( $arcavias, dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config' );

echo $init->getJsonRpcController()->process( $_REQUEST, 'php://input' );

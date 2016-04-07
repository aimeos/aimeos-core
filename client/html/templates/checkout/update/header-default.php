<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

/** client/html/checkout/standard/update/http-success
 * HTTP header sent for a successful status update response
 *
 * This HTTP header is returned to the remote system if the status
 * update was successful. It should be one of the 2xx HTTP headers.
 *
 * @param array List of valid HTTP headers
 * @since 2015.07
 * @category Developer
 * @see client/html/checkout/standard/update/http-error
 */
$default = $this->config( 'client/html/checkout/standard/update/http-success', array( 'HTTP/1.1 200 OK' ) );

foreach( $this->get( 'updateHttpHeaders', $default ) as $header ) {
	@header( $header );
}

echo $this->get( 'updateHeader' );

?>
<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Communication
 */

namespace Aimeos\MW\Communication;


/**
 * Common interface for communication with delivery and payment providers.
 *
 * @package MW
 * @subpackage Communication
 */
interface Iface
{
	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param string $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function transmit( $target, $method, $payload );
}
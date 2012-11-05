<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Communication
 * @version $Id: Interface.php 1317 2012-10-19 19:50:05Z nsendetzky $
 */

/**
 * Common interface for communication with delivery and payment providers.
 *
 * @package MW
 * @subpackage Communication
 */
interface MW_Communication_Interface
{
	/**
	 * Sends request parameters to the providers interface.
	 *
	 * @param string $target Receivers address e.g. url.
	 * @param string $method Initial method (e.g. post or get)
	 * @param mixed $payload Update information whose format depends on the payment provider
	 * @return string response body of a http request
	 */
	public function transmit( $target, $method, $payload );
}
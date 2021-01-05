<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Logger
 */


namespace Aimeos\MW\Logger;


/**
 * Generic minimal interface for logging messages
 *
 * @package MW
 * @subpackage Logger
 */
interface Iface
{
	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param int $prio Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function log( $message, int $prio = Base::ERR, string $facility = 'message' ) : Iface;
}

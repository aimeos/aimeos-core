<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Logger
 * @version $Id: Interface.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Generic minimal interface for logging messages
 *
 * @package MW
 * @subpackage Logger
 */
interface MW_Logger_Interface
{
	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' );
}

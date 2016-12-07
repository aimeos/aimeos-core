<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Logger
 */


namespace Aimeos\MW\Logger;


/**
 * Send log messages to several target loggers
 *
 * @package MW
 * @subpackage Logger
 */
class Compose extends \Aimeos\MW\Logger\Base implements \Aimeos\MW\Logger\Iface
{
	private $loggers;


	/**
	 * Initializes the logger object.
	 *
	 * @param array $loggers Instances of logger classes
	 */
	public function __construct( array $loggers )
	{
		$this->loggers = $loggers;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws \Aimeos\MW\Logger\Exception If the priority is invalid
	 * @see \Aimeos\MW\Logger\Base for available log level constants
	 */
	public function log( $message, $priority = \Aimeos\MW\Logger\Base::ERR, $facility = 'message' )
	{
		foreach( $this->loggers as $logger ) {
			$logger->log( $message, $priority, $facility );
		}
	}
}

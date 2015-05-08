<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Logger
 */


/**
 * Send log messages to several target loggers
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_Compose extends MW_Logger_Abstract implements MW_Logger_Interface
{
	private $_loggers;


	/**
	 * Initializes the logger object.
	 *
	 * @param array $loggers Instances of logger classes
	 */
	public function __construct( array $loggers )
	{
		$this->_loggers = $loggers;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If the priority is invalid
	 * @see MW_Logger_Abstract for available log level constants
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' )
	{
		foreach( $this->_loggers as $logger ) {
			$logger->log( $message, $priority, $facility );
		}
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages using Zend_Log.
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_Zend extends MW_Logger_Abstract implements MW_Logger_Interface
{
	private $_logger = null;


	/**
	 * Initializes the logger object.
	 *
	 * @param Zend_Log $logger Zend_Log object
	 */
	public function __construct(Zend_Log $logger)
	{
		$this->_logger = $logger;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If an error occurs in Zend_Log
	 * @see MW_Logger_Abstract for available log level constants
	 */
	public function log($message, $priority = MW_Logger_Abstract::ERR, $facility = 'message')
	{
		try
		{
			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$this->_logger->log( '<' . $facility . '> ' . $message, $priority );
		}
		catch( Zend_Log_Exception $ze )	{
			throw new MW_Logger_Exception( $ze->getMessage() );
		}
	}
}
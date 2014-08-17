<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages.
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_File extends MW_Logger_Abstract implements MW_Logger_Interface
{
	private $_loglevel = MW_Logger_Abstract::ERR;
	private $_stream;


	/**
	 * Initializes the logger object.
	 *
	 * @param string $filename Log file name
	 * @param integer $priority Default priority
	 */
	public function __construct( $filename, $priority = MW_Logger_Abstract::ERR )
	{
		if ( !$this->_stream = fopen( $filename, 'a', false ) ) {
			throw new MW_Logger_Exception( sprintf( 'Unable to open file "%1$s" for appending' ), $filename );
		}

		$this->_loglevel = $priority;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If an error occurs in Zend_Log
	 * @see MW_Logger_Abstract for available log level constants
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' )
	{
		if( $priority <= $this->_loglevel )
		{

			$this->_checkLogLevel( $priority );

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$message = '<' . $facility . '> ' . date( 'Y-m-d H:i:s' ) . ' ' . $priority . ' ' . $message;

			if ( false === fwrite( $this->_stream, $message ) ) {
				throw new MW_Logger_Exception( 'Unable to write to stream' );
			}
		}
	}
}
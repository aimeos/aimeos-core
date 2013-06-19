<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages to a database table.
  *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_DB extends MW_Logger_Abstract implements MW_Logger_Interface
{
	private $_stmt = null;
	private $_loglevel = MW_Logger_Abstract::ERR;
	private $_requestid = null;


	/**
	 * Initializes the logger object.
	 *
	 * The log statement must be like:
	 *		INSERT INTO logtable (facility, logtime, priority, message, requestid) VALUES (?, ?, ?, ?, ?)
	 *
	 * @param MW_DB_Statement_Interface $stmt Database statement object for inserting data
	 * @param integer $loglevel Minimum log level, messages with a less important log level will be discarded
	 */
	public function __construct( MW_DB_Statement_Interface $stmt, $loglevel = MW_Logger_Abstract::ERR, $requestid = null )
	{
		$this->_stmt = $stmt;
		$this->_loglevel = $loglevel;

		if( $requestid === null ) {
			$requestid = md5( php_uname('n') . getmypid() . date( 'Y-m-d H:i:s' ) );
		}
		$this->_requestid = $requestid;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If the priority is invalid
	 * @throws MW_DB_Exception If an error occurs while adding log message
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

			$this->_stmt->bind( 1, $facility );
			$this->_stmt->bind( 2, date( 'Y-m-d H:i:s' ) );
			$this->_stmt->bind( 3, $priority, MW_DB_Statement_Abstract::PARAM_INT );
			$this->_stmt->bind( 4, $message );
			$this->_stmt->bind( 5, $this->_requestid );
			$this->_stmt->execute()->finish();
		}
	}
}

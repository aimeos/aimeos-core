<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
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
	private $stmt;
	private $loglevel;
	private $requestid;
	private $facilities;


	/**
	 * Initializes the logger object.
	 *
	 * The log statement must be like:
	 *		INSERT INTO logtable (facility, logtime, priority, message, requestid) VALUES (?, ?, ?, ?, ?)
	 *
	 * @param MW_DB_Statement_Interface $stmt Database statement object for inserting data
	 * @param integer $priority Minimum priority for logging
	 * @param string|null $requestid Unique identifier for the request so multiple log entries which belong together can be found faster
	 * @param array|null $facilities Facilities for which messages should be logged
	 */
	public function __construct( MW_DB_Statement_Interface $stmt, $loglevel = MW_Logger_Abstract::ERR,
		$requestid = null, array $facilities = null )
	{
		$this->stmt = $stmt;
		$this->loglevel = $loglevel;
		$this->facilities = $facilities;

		if( $requestid === null ) {
			$requestid = md5( php_uname('n') . getmypid() . date( 'Y-m-d H:i:s' ) );
		}
		$this->requestid = $requestid;
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @throws MW_Logger_Exception If the priority is invalid
	 * @throws MW_DB_Exception If an error occurs while adding log message
	 * @see MW_Logger_Abstract for available log level constants
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' )
	{
		if( $priority <= $this->loglevel
			&& ( $this->facilities === null || in_array( $facility, $this->facilities ) ) )
		{
			$this->checkLogLevel( $priority );

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$this->stmt->bind( 1, $facility );
			$this->stmt->bind( 2, date( 'Y-m-d H:i:s' ) );
			$this->stmt->bind( 3, $priority, MW_DB_Statement_Abstract::PARAM_INT );
			$this->stmt->bind( 4, $message );
			$this->stmt->bind( 5, $this->requestid );
			$this->stmt->execute()->finish();
		}
	}
}

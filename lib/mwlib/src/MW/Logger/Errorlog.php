<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Logger
 */


/**
 * Log messages to the error_log file.
 *
 * @package MW
 * @subpackage Logger
 */
class MW_Logger_Errorlog extends MW_Logger_Abstract implements MW_Logger_Interface
{
	private $_loglevel;
	private $_facilities;


	/**
	 * Initializes the logger object.
	 *
	 * @param integer Log level from MW_Logger_Abstract
	 * @param array|null $facilities Facilities for which messages should be logged
	 */
	public function __construct( $loglevel = MW_Logger_Abstract::ERR, array $facilities = null )
	{
		$this->_loglevel = $loglevel;
		$this->_facilities = $facilities;
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
		if( $priority <= $this->_loglevel
			&& ( $this->_facilities === null || in_array( $facility, $this->_facilities ) ) )
		{
			switch( $priority )
			{
				// @codingStandardsIgnoreStart
				case MW_Logger_Abstract::EMERG: $level = '[emergency]'; break;
				case MW_Logger_Abstract::ALERT: $level = '[alert]'; break;
				case MW_Logger_Abstract::CRIT: $level = '[critical]'; break;
				case MW_Logger_Abstract::ERR: $level = '[error]'; break;
				case MW_Logger_Abstract::WARN: $level = '[warning]'; break;
				case MW_Logger_Abstract::NOTICE: $level = '[notice]'; break;
				case MW_Logger_Abstract::INFO: $level = '[info]'; break;
				case MW_Logger_Abstract::DEBUG: $level = '[debug]'; break;
				// @codingStandardsIgnoreEnd
				default:
					throw new MW_Logger_Exception( sprintf( 'Invalid log priority %1$s', $priority ) );
			}

			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			if( error_log( '<' . $facility . '> ' . $level . ' ' . $message ) === false ) {
				throw new MW_Logger_Exception( sprintf(
					'Unable to log message with priority "%1$d": %2$s', $priority, $message ) );
			}
		}
	}
}
